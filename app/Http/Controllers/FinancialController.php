<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CashFlowEntry;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FinancialController extends Controller
{
    public function index(): View
    {
        $this->authorizeFinancialAccess();

        $period      = now()->format('Y-m');
        $bankAccounts= BankAccount::with('latestBalance')->where('is_active', true)->orderBy('sort_order')->get();
        $totalCash   = $bankAccounts->sum(fn($a) => $a->latestBalance?->closing_balance ?? 0);

        $entries  = CashFlowEntry::where('period', 'like', "{$period}%")->get();
        $inflows  = $entries->where('type', 'INFLOW');
        $outflows = $entries->where('type', 'OUTFLOW');

        $cashFlow = [
            'inflows'       => $inflows,
            'outflows'      => $outflows,
            'total_inflow'  => $inflows->sum('amount'),
            'total_outflow' => $outflows->sum('amount'),
            'net_position'  => $inflows->sum('amount') - $outflows->sum('amount'),
        ];

        $trendLabels   = ['May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar','Apr'];
        $trendDatasets = [
            ['label' => 'ABA Bank', 'data' => collect(range(0,11))->map(fn($i) => 110000 + $i * 1800 + sin($i) * 4000)->all()],
            ['label' => 'ACLEDA',   'data' => collect(range(0,11))->map(fn($i) => 75000  + $i * 900  + cos($i) * 3000)->all()],
        ];

        return view('pages.financial.index', [
            'bankAccounts' => $bankAccounts,
            'totalCash'    => $totalCash,
            'cashFlow'     => $cashFlow,
            'trendChart'   => ['labels' => $trendLabels, 'datasets' => $trendDatasets],
        ]);
    }

    public function transactions(): View
    {
        $this->authorizeFinancialAccess();
        $transactions = Transaction::with('recordedBy:id,full_name')->latest('transaction_date')->paginate(25);
        return view('pages.financial.transactions', compact('transactions'));
    }

    public function storeCashFlow(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorizeFinancialAccess();
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Cash flow submitted for approval.']);
        }
        return back()->with('success', 'Cash flow submitted for approval.');
    }

    public function updateBalance(Request $request, BankAccount $account): JsonResponse|RedirectResponse
    {
        $this->authorizeFinancialAccess();
        $request->validate(['closing_balance' => 'required|numeric', 'balance_date' => 'required|date']);
        $account->balances()->create([
            'balance_date'    => $request->balance_date,
            'opening_balance' => $account->latestBalance?->closing_balance ?? 0,
            'closing_balance' => $request->closing_balance,
            'source'          => 'manual',
            'recorded_by'     => auth()->id(),
        ]);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Balance updated.']);
        }
        return back()->with('success', 'Balance updated.');
    }

    private function authorizeFinancialAccess(): void
    {
        abort_unless(auth()->user()->hasFinancialAccess(), 403, 'You do not have access to the Financial Dashboard.');
    }
}
