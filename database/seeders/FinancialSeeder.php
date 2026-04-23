<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\BankBalance;
use App\Models\CashFlowEntry;
use App\Models\CashFlowForecast;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class FinancialSeeder extends Seeder
{
    public function run(): void
    {
        $recorder = User::where('email', 'accountant@smrhotel.com')->first();
        $finance  = User::where('email', 'finance@smrhotel.com')->first();

        // ── Bank Accounts ────────────────────────────────────────
        $accounts = [
            [
                'code'          => 'ABA-USD-OPS',
                'name'          => 'ABA Operations (USD)',
                'bank'          => 'ABA Bank',
                'currency'      => 'USD',
                'type'          => 'current',
                'min_threshold' => 10000,
                'sort_order'    => 1,
                'is_active'     => true,
            ],
            [
                'code'          => 'ABA-KHR-OPS',
                'name'          => 'ABA Operations (KHR)',
                'bank'          => 'ABA Bank',
                'currency'      => 'KHR',
                'type'          => 'current',
                'min_threshold' => 40000000,
                'sort_order'    => 2,
                'is_active'     => true,
            ],
            [
                'code'          => 'ACLEDA-USD',
                'name'          => 'ACLEDA Reserve (USD)',
                'bank'          => 'ACLEDA Bank',
                'currency'      => 'USD',
                'type'          => 'savings',
                'min_threshold' => 25000,
                'sort_order'    => 3,
                'is_active'     => true,
            ],
            [
                'code'          => 'CASH-PETTY',
                'name'          => 'Petty Cash',
                'bank'          => 'Cash',
                'currency'      => 'USD',
                'type'          => 'petty_cash',
                'min_threshold' => 500,
                'sort_order'    => 4,
                'is_active'     => true,
            ],
        ];

        $accountModels = [];
        foreach ($accounts as $acct) {
            $accountModels[$acct['code']] = BankAccount::firstOrCreate(['code' => $acct['code']], $acct);
        }

        // ── Bank Balances (last 4 months) ─────────────────────────
        $balanceData = [
            'ABA-USD-OPS' => [
                ['date' => '2026-01-31', 'opening' => 45200.00, 'closing' => 52800.00],
                ['date' => '2026-02-28', 'opening' => 52800.00, 'closing' => 61500.00],
                ['date' => '2026-03-31', 'opening' => 61500.00, 'closing' => 74200.00],
                ['date' => '2026-04-21', 'opening' => 74200.00, 'closing' => 79800.00],
            ],
            'ACLEDA-USD' => [
                ['date' => '2026-01-31', 'opening' => 120000.00, 'closing' => 120000.00],
                ['date' => '2026-02-28', 'opening' => 120000.00, 'closing' => 145000.00],
                ['date' => '2026-03-31', 'opening' => 145000.00, 'closing' => 145000.00],
                ['date' => '2026-04-21', 'opening' => 145000.00, 'closing' => 145000.00],
            ],
            'CASH-PETTY' => [
                ['date' => '2026-01-31', 'opening' => 1200.00, 'closing' => 850.00],
                ['date' => '2026-02-28', 'opening' => 850.00,  'closing' => 950.00],
                ['date' => '2026-03-31', 'opening' => 950.00,  'closing' => 780.00],
                ['date' => '2026-04-21', 'opening' => 780.00,  'closing' => 620.00],
            ],
        ];

        foreach ($balanceData as $code => $records) {
            $account = $accountModels[$code] ?? null;
            if (!$account) continue;
            foreach ($records as $rec) {
                BankBalance::firstOrCreate(
                    ['bank_account_id' => $account->id, 'balance_date' => $rec['date']],
                    [
                        'opening_balance' => $rec['opening'],
                        'closing_balance' => $rec['closing'],
                        'source'          => 'manual',
                        'remark'          => 'Monthly reconciliation.',
                        'recorded_by'     => $recorder?->id,
                    ]
                );
            }
        }

        // ── Transactions ─────────────────────────────────────────
        $opsAccount = $accountModels['ABA-USD-OPS'] ?? null;
        if ($opsAccount) {
            $transactions = [
                // April 2026
                ['description' => 'Room Revenue — OTA Booking (Booking.com)', 'amount' => 12450.00, 'category_code' => 'room_revenue',    'date' => '2026-04-01'],
                ['description' => 'Room Revenue — Direct Booking',            'amount' => 8900.00,  'category_code' => 'room_revenue',    'date' => '2026-04-01'],
                ['description' => 'Restaurant Revenue — Lunch & Dinner',      'amount' => 3800.00,  'category_code' => 'fnb_revenue',     'date' => '2026-04-01'],
                ['description' => 'Laundry Supplies Purchase',                'amount' => -420.00,  'category_code' => 'hk_expense',      'date' => '2026-04-02'],
                ['description' => 'Staff Salary — April Advance',             'amount' => -8500.00, 'category_code' => 'payroll',         'date' => '2026-04-05'],
                ['description' => 'Room Revenue — Group Booking (10 rooms)',  'amount' => 4500.00,  'category_code' => 'room_revenue',    'date' => '2026-04-07'],
                ['description' => 'Utility Bill — Electricity',               'amount' => -2100.00, 'category_code' => 'utilities',       'date' => '2026-04-10'],
                ['description' => 'F&B Revenue — Bar & Drinks',               'amount' => 1950.00,  'category_code' => 'fnb_revenue',     'date' => '2026-04-10'],
                ['description' => 'Spa Revenue — Treatments',                 'amount' => 3200.00,  'category_code' => 'spa_revenue',     'date' => '2026-04-10'],
                ['description' => 'Linen & Towel Replacement',                'amount' => -1800.00, 'category_code' => 'hk_expense',      'date' => '2026-04-12'],
                ['description' => 'Marketing — Google Ads',                   'amount' => -650.00,  'category_code' => 'marketing',       'date' => '2026-04-12'],
                ['description' => 'Room Revenue — Weekend Walk-in',           'amount' => 2100.00,  'category_code' => 'room_revenue',    'date' => '2026-04-12'],
                ['description' => 'Food & Beverage Procurement',              'amount' => -3200.00, 'category_code' => 'fnb_cogs',        'date' => '2026-04-14'],
                ['description' => 'Staff Salary — Remaining April',           'amount' => -9200.00, 'category_code' => 'payroll',         'date' => '2026-04-15'],
                ['description' => 'Room Revenue — Booking.com Settlement',    'amount' => 11200.00, 'category_code' => 'room_revenue',    'date' => '2026-04-15'],
                ['description' => 'Maintenance — AC Service Contract',        'amount' => -880.00,  'category_code' => 'maintenance',     'date' => '2026-04-16'],
                ['description' => 'Restaurant Revenue',                       'amount' => 4100.00,  'category_code' => 'fnb_revenue',     'date' => '2026-04-16'],
                ['description' => 'Utility Bill — Water',                     'amount' => -380.00,  'category_code' => 'utilities',       'date' => '2026-04-18'],
                ['description' => 'Room Revenue — Direct Online',             'amount' => 6700.00,  'category_code' => 'room_revenue',    'date' => '2026-04-20'],
                ['description' => 'Spa Revenue — Spa Package Bookings',       'amount' => 2800.00,  'category_code' => 'spa_revenue',     'date' => '2026-04-20'],
                ['description' => 'Guest Amenities Restocking',               'amount' => -560.00,  'category_code' => 'hk_expense',      'date' => '2026-04-21'],

                // March 2026
                ['description' => 'Room Revenue — March Total Settlement',    'amount' => 38500.00, 'category_code' => 'room_revenue',    'date' => '2026-03-31'],
                ['description' => 'F&B Revenue — March Total',                'amount' => 15200.00, 'category_code' => 'fnb_revenue',     'date' => '2026-03-31'],
                ['description' => 'Staff Salaries — March',                   'amount' => -17800.00,'category_code' => 'payroll',         'date' => '2026-03-31'],
                ['description' => 'All Utilities — March',                    'amount' => -2850.00, 'category_code' => 'utilities',       'date' => '2026-03-31'],
                ['description' => 'F&B COGS — March',                         'amount' => -5100.00, 'category_code' => 'fnb_cogs',        'date' => '2026-03-31'],
                ['description' => 'Spa Revenue — March',                      'amount' => 4200.00,  'category_code' => 'spa_revenue',     'date' => '2026-03-31'],
            ];

            foreach ($transactions as $tx) {
                Transaction::firstOrCreate(
                    ['description' => $tx['description'], 'transaction_date' => $tx['date'], 'bank_account_id' => $opsAccount->id],
                    [
                        'amount'          => $tx['amount'],
                        'category_code'   => $tx['category_code'],
                        'recorded_by'     => $recorder?->id,
                    ]
                );
            }
        }

        // ── Cash Flow Entries ─────────────────────────────────────
        $cashFlowEntries = [
            // Revenue
            ['period' => '2026-01-01', 'category_code' => 'room_revenue',  'category' => 'Room Revenue',         'type' => 'INFLOW',  'amount' => 98500,  'note' => 'January room revenue'],
            ['period' => '2026-01-01', 'category_code' => 'fnb_revenue',   'category' => 'F&B Revenue',          'type' => 'INFLOW',  'amount' => 38200,  'note' => 'January F&B revenue'],
            ['period' => '2026-01-01', 'category_code' => 'spa_revenue',   'category' => 'Spa Revenue',          'type' => 'INFLOW',  'amount' => 9500,   'note' => 'January spa revenue'],
            ['period' => '2026-01-01', 'category_code' => 'payroll',       'category' => 'Staff Salaries',       'type' => 'OUTFLOW', 'amount' => 52000,  'note' => 'January payroll'],
            ['period' => '2026-01-01', 'category_code' => 'fnb_cogs',      'category' => 'F&B COGS',             'type' => 'OUTFLOW', 'amount' => 13400,  'note' => 'January F&B cost'],
            ['period' => '2026-01-01', 'category_code' => 'utilities',     'category' => 'Utilities',            'type' => 'OUTFLOW', 'amount' => 4800,   'note' => 'January utilities'],
            ['period' => '2026-01-01', 'category_code' => 'marketing',     'category' => 'Marketing',            'type' => 'OUTFLOW', 'amount' => 3200,   'note' => 'January marketing'],
            ['period' => '2026-01-01', 'category_code' => 'maintenance',   'category' => 'Maintenance',          'type' => 'OUTFLOW', 'amount' => 2800,   'note' => 'January maintenance'],

            ['period' => '2026-02-01', 'category_code' => 'room_revenue',  'category' => 'Room Revenue',         'type' => 'INFLOW',  'amount' => 108500, 'note' => 'February room revenue'],
            ['period' => '2026-02-01', 'category_code' => 'fnb_revenue',   'category' => 'F&B Revenue',          'type' => 'INFLOW',  'amount' => 41500,  'note' => 'February F&B revenue'],
            ['period' => '2026-02-01', 'category_code' => 'spa_revenue',   'category' => 'Spa Revenue',          'type' => 'INFLOW',  'amount' => 10800,  'note' => 'February spa revenue'],
            ['period' => '2026-02-01', 'category_code' => 'payroll',       'category' => 'Staff Salaries',       'type' => 'OUTFLOW', 'amount' => 53500,  'note' => 'February payroll'],
            ['period' => '2026-02-01', 'category_code' => 'fnb_cogs',      'category' => 'F&B COGS',             'type' => 'OUTFLOW', 'amount' => 14000,  'note' => 'February F&B cost'],
            ['period' => '2026-02-01', 'category_code' => 'utilities',     'category' => 'Utilities',            'type' => 'OUTFLOW', 'amount' => 4600,   'note' => 'February utilities'],

            ['period' => '2026-03-01', 'category_code' => 'room_revenue',  'category' => 'Room Revenue',         'type' => 'INFLOW',  'amount' => 118200, 'note' => 'March room revenue'],
            ['period' => '2026-03-01', 'category_code' => 'fnb_revenue',   'category' => 'F&B Revenue',          'type' => 'INFLOW',  'amount' => 46800,  'note' => 'March F&B revenue'],
            ['period' => '2026-03-01', 'category_code' => 'spa_revenue',   'category' => 'Spa Revenue',          'type' => 'INFLOW',  'amount' => 12500,  'note' => 'March spa revenue'],
            ['period' => '2026-03-01', 'category_code' => 'payroll',       'category' => 'Staff Salaries',       'type' => 'OUTFLOW', 'amount' => 55000,  'note' => 'March payroll'],
            ['period' => '2026-03-01', 'category_code' => 'fnb_cogs',      'category' => 'F&B COGS',             'type' => 'OUTFLOW', 'amount' => 14700,  'note' => 'March F&B cost'],
            ['period' => '2026-03-01', 'category_code' => 'utilities',     'category' => 'Utilities',            'type' => 'OUTFLOW', 'amount' => 5100,   'note' => 'March utilities'],
            ['period' => '2026-03-01', 'category_code' => 'marketing',     'category' => 'Marketing',            'type' => 'OUTFLOW', 'amount' => 3800,   'note' => 'March marketing (peak season prep)'],
        ];

        foreach ($cashFlowEntries as $entry) {
            CashFlowEntry::firstOrCreate(
                ['period' => $entry['period'], 'category_code' => $entry['category_code']],
                [
                    'category'        => $entry['category'],
                    'type'            => $entry['type'],
                    'amount'          => $entry['amount'],
                    'note'            => $entry['note'],
                    'submitted_by'    => $recorder?->id,
                    'approval_status' => 'APPROVED',
                ]
            );
        }

        // ── Cash Flow Forecasts (weekly) ──────────────────────────
        $forecasts = [
            ['iso_year' => 2026, 'iso_week' => 14, 'week_start' => '2026-04-01', 'week_end' => '2026-04-07', 'forecast' => 42000, 'actual' => 44500],
            ['iso_year' => 2026, 'iso_week' => 15, 'week_start' => '2026-04-08', 'week_end' => '2026-04-14', 'forecast' => 44000, 'actual' => 47200],
            ['iso_year' => 2026, 'iso_week' => 16, 'week_start' => '2026-04-15', 'week_end' => '2026-04-21', 'forecast' => 46000, 'actual' => 48900],
            ['iso_year' => 2026, 'iso_week' => 17, 'week_start' => '2026-04-22', 'week_end' => '2026-04-28', 'forecast' => 48000, 'actual' => null],
            ['iso_year' => 2026, 'iso_week' => 18, 'week_start' => '2026-04-29', 'week_end' => '2026-05-05', 'forecast' => 50000, 'actual' => null],
        ];

        foreach ($forecasts as $f) {
            CashFlowForecast::firstOrCreate(
                ['iso_year' => $f['iso_year'], 'iso_week' => $f['iso_week']],
                [
                    'week_start'       => $f['week_start'],
                    'week_end'         => $f['week_end'],
                    'forecast_amount'  => $f['forecast'],
                    'actual_amount'    => $f['actual'],
                    'alert_triggered'  => false,
                    'updated_by'       => $finance?->id,
                    'notes'            => 'Seasonal adjustment applied.',
                ]
            );
        }
    }
}
