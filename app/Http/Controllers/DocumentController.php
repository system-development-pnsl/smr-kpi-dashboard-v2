<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDocumentWithAI;
use App\Models\Department;
use App\Models\Document;
use App\Services\AiDocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(private readonly AiDocumentService $aiService) {}

    public function index(): View
    {
        $user  = auth()->user();
        $query = Document::with(['uploadedBy:id,full_name', 'department:id,code,label'])->latest();
        if ($user->role === 'head_of_dept') {
            $query->where('department_id', $user->department_id);
        }
        return view('pages.documents.index', ['documents' => $query->paginate(20), 'departments' => Department::orderBy('sort_order')->get()]);
    }

    public function upload(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'file'          => 'required|file|max:25600|mimes:pdf,docx,doc,xlsx,xls,csv,png,jpg,jpeg',
            'department_id' => 'required|exists:departments,id',
            'description'   => 'nullable|string|max:500',
        ]);

        $file     = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs('documents/' . now()->format('Y/m'), $filename, 'private');

        $doc = Document::create([
            'original_name' => $file->getClientOriginalName(),
            'stored_path'   => $path,
            'mime_type'     => $file->getMimeType(),
            'size_bytes'    => $file->getSize(),
            'file_type'     => strtoupper($file->getClientOriginalExtension()),
            'sha256'        => hash_file('sha256', $file->getRealPath()),
            'department_id' => $request->department_id,
            'description'   => $request->description,
            'uploaded_by'   => auth()->id(),
            'ai_status'     => 'pending',
        ]);

        ProcessDocumentWithAI::dispatch($doc);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Document uploaded. AI processing started.', 'redirect' => route('documents.show', $doc)]);
        }
        return redirect()->route('documents.show', $doc)->with('success', 'Document uploaded. AI processing started.');
    }

    public function show(Document $document): View
    {
        return view('pages.documents.show', compact('document'));
    }

    public function confirm(Request $request, Document $document): JsonResponse|RedirectResponse
    {
        $request->validate(['confirmed_fields' => 'required|array']);
        $document->update(['confirmed_fields' => $request->confirmed_fields, 'ai_status' => 'confirmed', 'confirmed_at' => now(), 'confirmed_by' => auth()->id()]);
        $this->aiService->pushToDashboard($document, $request->confirmed_fields);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Data confirmed and pushed to dashboard.', 'redirect' => route('documents.index')]);
        }
        return redirect()->route('documents.index')->with('success', 'Data confirmed and pushed to dashboard.');
    }

    public function destroy(Document $document): JsonResponse|RedirectResponse
    {
        Storage::disk('private')->delete($document->stored_path);
        $document->delete();
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Document deleted.']);
        }
        return redirect()->route('documents.index')->with('success', 'Document deleted.');
    }

    public function download(Document $document)
    {
        return Storage::disk('private')->download($document->stored_path, $document->original_name);
    }
}
