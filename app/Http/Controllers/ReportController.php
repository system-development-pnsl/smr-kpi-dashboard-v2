<?php

namespace App\Http\Controllers;

use App\Models\MonthlyReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = MonthlyReport::with('department')->orderByDesc('period')->paginate(20);
        return view('pages.reports.index', compact('reports'));
    }

    public function show(MonthlyReport $report): View
    {
        return view('pages.reports.show', compact('report'));
    }

    public function download(MonthlyReport $report)
    {
        if (! $report->pdf_path || ! Storage::disk('private')->exists($report->pdf_path)) {
            return back()->with('error', 'PDF not yet generated.');
        }
        return Storage::disk('private')->download($report->pdf_path, "SMR_Report_{$report->period->format('Y-m')}.pdf");
    }
}
