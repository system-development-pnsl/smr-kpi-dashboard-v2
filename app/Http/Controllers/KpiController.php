<?php

namespace App\Http\Controllers;

use App\Models\ActionPlan;
use App\Models\Kpi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KpiController extends Controller
{
    public function index(Request $request): View
    {
        $user  = auth()->user();
        $query = Kpi::with(['department:id,code,label,color', 'latestEntry'])->where('is_active', true);

        if ($user->role === 'head_of_dept') {
            $query->where('department_id', $user->department_id);
        }

        $kpis = $query->get()->each(fn($k) => $k->history = $k->entries()->latest('period')->limit(12)->pluck('value')->reverse()->values());

        if ($request->filled('status') && $request->status !== '') {
            $kpis = $kpis->filter(fn($k) => match ($request->status) {
                'on_track'    => $k->currentStatus() === 'green',
                'near_target' => $k->currentStatus() === 'amber',
                'off_track'   => $k->currentStatus() === 'red',
                default       => true,
            });
        }

        $summary = [
            'on_track'  => $query->get()->filter(fn($k) => $k->currentStatus() === 'green')->count(),
            'at_risk'   => $query->get()->filter(fn($k) => $k->currentStatus() === 'amber')->count(),
            'off_track' => $query->get()->filter(fn($k) => $k->currentStatus() === 'red')->count(),
        ];

        $actionPlans = ActionPlan::with(['department', 'goals.actionItems'])->where('year', now()->year)
            ->where('quarter', ceil(now()->month / 3))->get();

        return view('pages.kpi.index', compact('kpis', 'summary', 'actionPlans'));
    }

    public function show(Kpi $kpi): View
    {
        $kpi->load(['department', 'entries' => fn($q) => $q->latest('period')->limit(24)]);
        return view('pages.kpi.show', compact('kpi'));
    }

    public function addEntry(Request $request, Kpi $kpi): JsonResponse|RedirectResponse
    {
        $request->validate(['value' => 'required|numeric', 'period' => 'required|date', 'note' => 'nullable|string|max:500']);
        $kpi->entries()->create(['value' => $request->value, 'period' => $request->period, 'note' => $request->note, 'source' => 'manual', 'submitted_by' => auth()->id()]);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'KPI value submitted.']);
        }
        return back()->with('success', 'KPI value submitted.');
    }

    public function actionPlan(ActionPlan $plan): View
    {
        $plan->load(['department', 'goals.actionItems.assignee', 'creator']);
        return view('pages.kpi.action-plan', compact('plan'));
    }

    public function addGoal(Request $request, ActionPlan $plan): JsonResponse|RedirectResponse
    {
        $request->validate(['title' => 'required|string|max:200', 'target_date' => 'required|date', 'owner_id' => 'required|exists:users,id']);
        $plan->goals()->create([...$request->only('title', 'description', 'target_date', 'owner_id'), 'status' => 'on_track', 'sort_order' => $plan->goals()->count()]);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Goal added.']);
        }
        return back()->with('success', 'Goal added.');
    }
}
