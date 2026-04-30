<?php

namespace Database\Seeders;

use App\Models\ActionPlan;
use App\Models\Department;
use App\Models\GoalActionItem;
use App\Models\StrategicGoal;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $gm    = User::where('email', 'gm@sunmoon.hotel')->first();
        $agm   = User::where('email', 'agm@smrhotel.com')->first();
        $fin   = User::where('email', 'finance@smrhotel.com')->first();
        $hr    = User::where('email', 'hr@smrhotel.com')->first();
        $sales = User::where('email', 'sales@smrhotel.com')->first();
        $fo    = User::where('email', 'fo.manager@smrhotel.com')->first();
        $maint = User::where('email', 'maintenance@smrhotel.com')->first();

        $execDept  = Department::where('code', 'EXEC')->first();
        $finDept   = Department::where('code', 'FIN')->first();
        $hrDept    = Department::where('code', 'HR')->first();
        $salesDept = Department::where('code', 'SALES')->first();

        // ── Q2 2026 Executive Action Plan ─────────────────────────
        $plan = ActionPlan::firstOrCreate(
            ['department_id' => $execDept?->id, 'quarter' => 2, 'year' => 2026],
            [
                'mission'       => 'Achieve peak season revenue targets, improve guest satisfaction to 8.5+, and reduce operating cost ratio below 60% through operational excellence and strategic partnerships.',
                'created_by'    => $gm?->id,
                'submitted_at'  => '2026-04-01 09:00:00',
                'acknowledged_at' => '2026-04-03 14:30:00',
                'acknowledged_by' => $agm?->id,
            ]
        );

        $goals = [
            [
                'title'       => 'Revenue Growth — Achieve 12% increase vs Q2 2025',
                'description' => 'Drive revenue through direct booking campaigns, OTA rate optimization, and corporate account development. Target: $550,000 total quarterly revenue.',
                'target_date' => '2026-06-30',
                'owner_id'    => $sales?->id,
                'status'      => 'on_track',
                'sort_order'  => 1,
                'items' => [
                    ['description' => 'Launch direct booking discount campaign on hotel website', 'due_date' => '2026-04-30', 'assignee_id' => $sales?->id, 'completion_pct' => 80, 'status' => 'in_progress'],
                    ['description' => 'Negotiate new corporate rate agreements with 5 companies',  'due_date' => '2026-05-15', 'assignee_id' => $sales?->id, 'completion_pct' => 40, 'status' => 'in_progress'],
                    ['description' => 'Optimize OTA channel distribution and pricing strategy',    'due_date' => '2026-05-01', 'assignee_id' => $sales?->id, 'completion_pct' => 60, 'status' => 'in_progress'],
                    ['description' => 'Develop package deals (room + spa + tours)',               'due_date' => '2026-04-25', 'assignee_id' => $agm?->id,  'completion_pct' => 90, 'status' => 'in_progress'],
                ],
            ],
            [
                'title'       => 'Guest Experience Enhancement — NPS above 8.5',
                'description' => 'Improve guest satisfaction scores through service training, amenity upgrades, and streamlined check-in/out processes.',
                'target_date' => '2026-06-30',
                'owner_id'    => $fo?->id,
                'status'      => 'on_track',
                'sort_order'  => 2,
                'items' => [
                    ['description' => 'Conduct full service standards training for all guest-facing staff', 'due_date' => '2026-05-10', 'assignee_id' => $hr?->id,  'completion_pct' => 50, 'status' => 'in_progress'],
                    ['description' => 'Implement mobile check-in/check-out for returning guests',          'due_date' => '2026-05-31', 'assignee_id' => $fo?->id,  'completion_pct' => 20, 'status' => 'in_progress'],
                    ['description' => 'Refresh in-room amenities and welcome pack',                        'due_date' => '2026-05-15', 'assignee_id' => $agm?->id, 'completion_pct' => 30, 'status' => 'in_progress'],
                    ['description' => 'Install tablet-based guest feedback system in all rooms',           'due_date' => '2026-06-15', 'assignee_id' => $fo?->id,  'completion_pct' => 0,  'status' => 'not_started'],
                ],
            ],
            [
                'title'       => 'Cost Optimization — Reduce operating cost ratio to <60%',
                'description' => 'Identify and execute cost reduction initiatives across all departments without compromising service quality.',
                'target_date' => '2026-06-30',
                'owner_id'    => $fin?->id,
                'status'      => 'at_risk',
                'sort_order'  => 3,
                'items' => [
                    ['description' => 'Complete energy audit and implement top 3 recommendations', 'due_date' => '2026-05-31', 'assignee_id' => $maint?->id, 'completion_pct' => 10, 'status' => 'in_progress'],
                    ['description' => 'Renegotiate supplier contracts for F&B procurement',        'due_date' => '2026-05-15', 'assignee_id' => $fin?->id,  'completion_pct' => 60, 'status' => 'in_progress'],
                    ['description' => 'Review and reduce OTA commissions through direct booking shift', 'due_date' => '2026-06-01', 'assignee_id' => $sales?->id, 'completion_pct' => 40, 'status' => 'in_progress'],
                    ['description' => 'Implement waste tracking system in F&B to reduce food waste', 'due_date' => '2026-05-20', 'assignee_id' => $fin?->id, 'completion_pct' => 0, 'status' => 'not_started'],
                ],
            ],
            [
                'title'       => 'Workforce Development — Training & Retention',
                'description' => 'Reduce staff turnover below 15% and ensure all staff complete minimum 20 training hours for the quarter.',
                'target_date' => '2026-06-30',
                'owner_id'    => $hr?->id,
                'status'      => 'on_track',
                'sort_order'  => 4,
                'items' => [
                    ['description' => 'Launch Q2 training calendar for all departments',              'due_date' => '2026-04-25', 'assignee_id' => $hr?->id, 'completion_pct' => 100, 'status' => 'completed'],
                    ['description' => 'Conduct monthly stay-interview with all team leaders',         'due_date' => '2026-06-30', 'assignee_id' => $hr?->id, 'completion_pct' => 33,  'status' => 'in_progress'],
                    ['description' => 'Implement Employee of the Month recognition program',          'due_date' => '2026-05-01', 'assignee_id' => $hr?->id, 'completion_pct' => 80,  'status' => 'in_progress'],
                    ['description' => 'Complete peak season hiring (8 temporary staff)',              'due_date' => '2026-04-30', 'assignee_id' => $hr?->id, 'completion_pct' => 50,  'status' => 'in_progress'],
                ],
            ],
        ];

        foreach ($goals as $goalData) {
            $items = $goalData['items'] ?? [];
            unset($goalData['items']);

            $goal = StrategicGoal::firstOrCreate(
                ['action_plan_id' => $plan->id, 'title' => $goalData['title']],
                array_merge($goalData, ['action_plan_id' => $plan->id])
            );

            foreach ($items as $i => $item) {
                if (!$item['assignee_id']) continue;
                GoalActionItem::firstOrCreate(
                    ['strategic_goal_id' => $goal->id, 'description' => $item['description']],
                    [
                        'due_date'      => $item['due_date'],
                        'assignee_id'   => $item['assignee_id'],
                        'completion_pct' => $item['completion_pct'],
                        'status'        => $item['status'],
                        'sort_order'    => $i + 1,
                        'completed_at'  => $item['status'] === 'completed' ? now() : null,
                    ]
                );
            }
        }

        // ── Q1 2026 Finance Action Plan (completed) ───────────────
        $q1Plan = ActionPlan::firstOrCreate(
            ['department_id' => $finDept?->id, 'quarter' => 1, 'year' => 2026],
            [
                'mission'          => 'Close Q1 books accurately, submit statutory filings, and establish financial control baseline for 2026.',
                'created_by'       => $fin?->id,
                'submitted_at'     => '2026-01-10 09:00:00',
                'acknowledged_at'  => '2026-01-12 11:00:00',
                'acknowledged_by'  => $gm?->id,
            ]
        );

        $q1Goals = [
            [
                'title'       => 'Q1 Financial Reporting & Tax Filing',
                'description' => 'Complete Q1 P&L, balance sheet, and cash flow report. Submit VAT and income tax filings by deadline.',
                'target_date' => '2026-04-15',
                'owner_id'    => $fin?->id,
                'status'      => 'completed',
                'sort_order'  => 1,
                'items' => [
                    ['description' => 'Reconcile all bank accounts for March 2026',           'due_date' => '2026-04-05', 'assignee_id' => $fin?->id, 'completion_pct' => 100, 'status' => 'completed'],
                    ['description' => 'Prepare Q1 P&L and balance sheet',                    'due_date' => '2026-04-10', 'assignee_id' => $fin?->id, 'completion_pct' => 100, 'status' => 'completed'],
                    ['description' => 'Submit VAT return for Q1',                            'due_date' => '2026-04-15', 'assignee_id' => $fin?->id, 'completion_pct' => 100, 'status' => 'completed'],
                    ['description' => 'Present Q1 results to GM and ownership group',        'due_date' => '2026-04-15', 'assignee_id' => $fin?->id, 'completion_pct' => 100, 'status' => 'completed'],
                ],
            ],
        ];

        foreach ($q1Goals as $goalData) {
            $items = $goalData['items'] ?? [];
            unset($goalData['items']);

            $goal = StrategicGoal::firstOrCreate(
                ['action_plan_id' => $q1Plan->id, 'title' => $goalData['title']],
                array_merge($goalData, ['action_plan_id' => $q1Plan->id])
            );

            foreach ($items as $i => $item) {
                if (!$item['assignee_id']) continue;
                GoalActionItem::firstOrCreate(
                    ['strategic_goal_id' => $goal->id, 'description' => $item['description']],
                    [
                        'due_date'      => $item['due_date'],
                        'assignee_id'   => $item['assignee_id'],
                        'completion_pct' => $item['completion_pct'],
                        'status'        => $item['status'],
                        'sort_order'    => $i + 1,
                        'completed_at'  => now()->subDays(5),
                    ]
                );
            }
        }
    }
}
