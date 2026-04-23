<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Kpi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AiDocumentService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key', '');
        $this->model  = config('services.anthropic.model', 'claude-sonnet-4-5');
    }

    /**
     * Extract structured KPI data from a document using the Anthropic API.
     */
    public function extract(Document $document): array
    {
        $content = $this->readFileContent($document);
        $prompt  = $this->buildExtractionPrompt($document, $content);

        $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
            ])
            ->timeout(90)
            ->post('https://api.anthropic.com/v1/messages', [
                'model'      => $this->model,
                'max_tokens' => 4096,
                'system'     => 'You are a hotel operations data extraction expert. Return only valid JSON.',
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                'Anthropic API error ' . $response->status() . ': ' . $response->body()
            );
        }

        $text = $response->json('content.0.text', '');

        return $this->parseJson($text);
    }

    /**
     * After HOD confirms extraction — push confirmed field data into dashboard tables.
     */
    public function pushToDashboard(Document $document, array $fields): void
    {
        foreach ($fields as $field) {
            if (!is_array($field)) continue;

            match ($field['target_module'] ?? null) {
                'kpi'     => $this->pushToKpi($document, $field),
                'task'    => $this->pushToTask($document, $field),
                'finance' => $this->pushToFinance($document, $field),
                default   => null,
            };
        }
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function buildExtractionPrompt(Document $document, string $content): string
    {
        $dept    = $document->department?->label ?? 'Unknown';
        $period  = now()->format('F Y');
        $kpiList = $this->getDeptKpiList($document->department_id);

        return <<<PROMPT
        CONTEXT:
        - Property: Sun & Moon Riverside Hotel, Phnom Penh, Cambodia
        - Department: {$dept}
        - Reporting Period: {$period}
        - Document: {$document->original_name}

        TASK:
        Extract all operational data from the document and return ONLY a JSON object.
        No prose, no markdown fences, no explanation.

        KNOWN KPI NAMES FOR THIS DEPARTMENT:
        {$kpiList}

        OUTPUT SCHEMA:
        {
          "extracted_fields": [
            {
              "field_name": "human readable name",
              "field_key": "snake_case_key matching known KPIs if possible",
              "value": null,
              "unit": "%, USD, count, minutes, score, hours",
              "period": "YYYY-MM-DD or null",
              "confidence": 0.0,
              "target_module": "kpi | task | finance | action_plan | null",
              "source_text": "exact sentence this was extracted from"
            }
          ],
          "document_summary": "1-2 sentence summary",
          "unrecognized_items": []
        }

        STRICT RULES:
        - NEVER invent or hallucinate numeric values. Return null if not clearly present.
        - Set confidence below 0.8 for anything uncertain.
        - Dates must be YYYY-MM-DD format.
        - Numbers must match exactly what appears in the document.

        DOCUMENT CONTENT:
        {$content}
        PROMPT;
    }

    private function readFileContent(Document $document): string
    {
        $path = Storage::disk('private')->path($document->stored_path);

        if (!file_exists($path)) {
            return '[File not found on disk]';
        }

        return match ($document->file_type) {
            'PDF'         => $this->extractPdf($path),
            'DOCX', 'DOC' => $this->extractDocx($path),
            'XLSX', 'XLS' => $this->extractExcel($path),
            'CSV'         => mb_substr((string) file_get_contents($path), 0, 8000),
            'PNG', 'JPG', 'JPEG' => '[Image file — text extraction not supported]',
            default       => '[Unsupported file type]',
        };
    }

    private function extractPdf(string $path): string
    {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf    = $parser->parseFile($path);
            $text   = $pdf->getText();

            if (trim($text) === '') {
                return '[PDF contains no extractable text — may be a scanned image]';
            }

            return mb_substr(trim($text), 0, 8000);
        } catch (\Throwable $e) {
            Log::warning('PDF extraction failed', ['path' => $path, 'error' => $e->getMessage()]);
            return '[PDF extraction failed: ' . $e->getMessage() . ']';
        }
    }

    private function extractDocx(string $path): string
    {
        $factory = 'PhpOffice\PhpWord\IOFactory';
        if (!class_exists($factory)) {
            return '[Install phpoffice/phpword for DOCX extraction: composer require phpoffice/phpword]';
        }

        try {
            /** @var object $word */
            $word = $factory::load($path);
            $text = '';
            foreach ($word->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
            return mb_substr($text, 0, 8000);
        } catch (\Throwable $e) {
            Log::warning('DOCX extraction failed', ['path' => $path, 'error' => $e->getMessage()]);
            return '[DOCX extraction failed]';
        }
    }

    private function extractExcel(string $path): string
    {
        $factory = 'PhpOffice\PhpSpreadsheet\IOFactory';
        if (!class_exists($factory)) {
            return '[Install phpoffice/phpspreadsheet for Excel extraction: composer require phpoffice/phpspreadsheet]';
        }

        try {
            /** @var object $spreadsheet */
            $spreadsheet = $factory::load($path);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = [];

            foreach ($sheet->getRowIterator() as $row) {
                $cells = [];
                foreach ($row->getCellIterator() as $cell) {
                    $val = $cell->getValue();
                    if ($val !== null && $val !== '') {
                        $cells[] = (string) $val;
                    }
                }
                if (!empty($cells)) {
                    $rows[] = implode(' | ', $cells);
                }
            }

            return mb_substr(implode("\n", $rows), 0, 8000);
        } catch (\Throwable $e) {
            Log::warning('Excel extraction failed', ['path' => $path, 'error' => $e->getMessage()]);
            return '[Excel extraction failed]';
        }
    }

    private function parseJson(string $text): array
    {
        // Strip markdown code fences if present
        $text = (string) preg_replace('/^```(?:json)?\s*/m', '', $text);
        $text = (string) preg_replace('/\s*```\s*$/m', '', $text);
        $text = trim($text);

        $data = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            throw new \RuntimeException('AI returned invalid JSON: ' . json_last_error_msg());
        }

        return $data;
    }

    private function getDeptKpiList(?int $departmentId): string
    {
        if ($departmentId === null) {
            return '(no department context)';
        }

        $list = Kpi::where('department_id', $departmentId)->pluck('name');

        if ($list->isEmpty()) {
            return '(no KPIs configured for this department)';
        }

        return $list->map(fn(string $n) => "- {$n}")->implode("\n");
    }

    private function pushToKpi(Document $document, array $field): void
    {
        if ($field['value'] === null) return;

        $kpi = Kpi::where('department_id', $document->department_id)
            ->where(fn($q) =>
                $q->where('name', 'like', '%' . ($field['field_name'] ?? '') . '%')
                  ->orWhere('slug', $field['field_key'] ?? '')
            )->first();

        if ($kpi) {
            $kpi->entries()->create([
                'value'        => (float) $field['value'],
                'period'       => $field['period'] ?? now()->startOfMonth()->toDateString(),
                'source'       => 'ai',
                'note'         => 'Auto-extracted from: ' . $document->original_name,
                'submitted_by' => $document->uploaded_by,
            ]);
        }
    }

    private function pushToTask(Document $document, array $field): void
    {
        Log::info('AI task push', ['field' => $field['field_name'] ?? '?', 'doc' => $document->id]);
    }

    private function pushToFinance(Document $document, array $field): void
    {
        Log::info('AI finance push', ['field' => $field['field_name'] ?? '?', 'doc' => $document->id]);
    }
}
