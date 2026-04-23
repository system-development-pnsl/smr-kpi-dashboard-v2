<?php

namespace App\Jobs;

use App\Events\DocumentExtractionCompleted;
use App\Models\Document;
use App\Services\AiDocumentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDocumentWithAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 120;

    public function __construct(public readonly Document $document) {}

    public function handle(AiDocumentService $ai): void
    {
        $this->document->update(['ai_status' => 'processing']);

        try {
            $data = $ai->extract($this->document);

            $this->document->update([
                'ai_status'      => 'extracted',
                'extracted_data' => $data,
            ]);

            DocumentExtractionCompleted::dispatch($this->document->fresh());
        } catch (\Throwable $e) {
            Log::error('AI document extraction failed', [
                'document_id' => $this->document->id,
                'error'       => $e->getMessage(),
            ]);

            $this->document->update(['ai_status' => 'failed']);

            DocumentExtractionCompleted::dispatch($this->document->fresh());

            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        $this->document->update(['ai_status' => 'failed']);
    }
}
