<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $gm    = User::where('email', 'gm@smrhotel.com')->first();
        $agm   = User::where('email', 'agm@smrhotel.com')->first();
        $fin   = User::where('email', 'finance@smrhotel.com')->first();
        $hr    = User::where('email', 'hr@smrhotel.com')->first();
        $fo    = User::where('email', 'fo.manager@smrhotel.com')->first();
        $fnb   = User::where('email', 'fnb.manager@smrhotel.com')->first();
        $hk    = User::where('email', 'hk.manager@smrhotel.com')->first();
        $sales = User::where('email', 'sales@smrhotel.com')->first();
        $maint = User::where('email', 'maintenance@smrhotel.com')->first();
        $rec   = User::where('email', 'reception@smrhotel.com')->first();

        $foDept    = Department::where('code', 'FO')->first();
        $fnbDept   = Department::where('code', 'FNB')->first();
        $hkDept    = Department::where('code', 'HK')->first();
        $finDept   = Department::where('code', 'FIN')->first();
        $hrDept    = Department::where('code', 'HR')->first();
        $maintDept = Department::where('code', 'MAINT')->first();
        $salesDept = Department::where('code', 'SALES')->first();
        $execDept  = Department::where('code', 'EXEC')->first();

        $tasks = [
            [
                'task_number'  => 'TSK-001',
                'title'        => 'Q2 Revenue Forecast Presentation',
                'description'  => 'Prepare comprehensive Q2 revenue forecast including room revenue, F&B, spa, and ancillary services. Present to ownership by end of month.',
                'priority'     => 'P1',
                'status'       => 'IN_PROGRESS',
                'due_date'     => '2026-04-30',
                'start_date'   => '2026-04-15',
                'department_id'=> $finDept?->id,
                'created_by'   => $gm?->id,
                'category'     => 'reporting',
                'tags'         => ['finance', 'quarterly', 'forecast'],
                'assignees'    => [$fin?->id, $agm?->id],
                'comments'     => [
                    ['user_id' => $fin?->id,  'body' => 'Draft completed for room revenue section. Moving to F&B next.'],
                    ['user_id' => $agm?->id,  'body' => 'Please include comparison with Q2 2025 actuals.'],
                ],
            ],
            [
                'task_number'  => 'TSK-002',
                'title'        => 'Peak Season Staffing Plan — May to August',
                'description'  => 'Review staffing levels for peak season. Identify gaps, coordinate with department heads for temporary hire needs, and post job openings by April 25.',
                'priority'     => 'P1',
                'status'       => 'IN_PROGRESS',
                'due_date'     => '2026-04-25',
                'start_date'   => '2026-04-10',
                'department_id'=> $hrDept?->id,
                'created_by'   => $gm?->id,
                'category'     => 'hr',
                'tags'         => ['staffing', 'peak-season', 'hiring'],
                'assignees'    => [$hr?->id],
                'comments'     => [
                    ['user_id' => $hr?->id,  'body' => 'Completed review for FO and HK. FNB still pending.'],
                    ['user_id' => $fnb?->id, 'body' => 'FNB needs 3 additional wait staff and 1 sous chef.'],
                ],
            ],
            [
                'task_number'  => 'TSK-003',
                'title'        => 'Restaurant Menu Revamp for Summer Season',
                'description'  => 'Update restaurant menu to feature locally-sourced ingredients and seasonal Cambodian dishes. Coordinate with chef for tasting event.',
                'priority'     => 'P1',
                'status'       => 'IN_PROGRESS',
                'due_date'     => '2026-05-01',
                'start_date'   => '2026-04-01',
                'department_id'=> $fnbDept?->id,
                'created_by'   => $fnb?->id,
                'category'     => 'operations',
                'tags'         => ['menu', 'summer', 'food'],
                'assignees'    => [$fnb?->id],
                'comments'     => [
                    ['user_id' => $fnb?->id, 'body' => 'Draft menu is 60% complete. Waiting on pricing from procurement.'],
                ],
            ],
            [
                'task_number'  => 'TSK-004',
                'title'        => 'PMS System Upgrade — Front Desk Module',
                'description'  => 'Coordinate with vendor for Property Management System upgrade. Test new check-in/check-out flow and train front desk staff.',
                'priority'     => 'P2',
                'status'       => 'TODO',
                'due_date'     => '2026-05-15',
                'start_date'   => '2026-05-01',
                'department_id'=> $foDept?->id,
                'created_by'   => $agm?->id,
                'category'     => 'it',
                'tags'         => ['pms', 'upgrade', 'training'],
                'assignees'    => [$fo?->id, $rec?->id],
                'comments'     => [],
            ],
            [
                'task_number'  => 'TSK-005',
                'title'        => 'Monthly Deep Clean — All Guest Rooms',
                'description'  => 'Schedule and execute monthly deep clean for all 45 guest rooms. Focus on grout, carpet, upholstery, and balcony areas.',
                'priority'     => 'P2',
                'status'       => 'IN_PROGRESS',
                'due_date'     => '2026-04-28',
                'start_date'   => '2026-04-22',
                'department_id'=> $hkDept?->id,
                'created_by'   => $hk?->id,
                'category'     => 'housekeeping',
                'tags'         => ['cleaning', 'rooms', 'monthly'],
                'assignees'    => [$hk?->id],
                'comments'     => [
                    ['user_id' => $hk?->id, 'body' => 'Floors 1-3 complete. Floors 4-5 scheduled for tomorrow.'],
                ],
            ],
            [
                'task_number'  => 'TSK-006',
                'title'        => 'Social Media Content Calendar — May',
                'description'  => 'Create Instagram and Facebook content calendar for May. Include promotions, local events near hotel, and behind-the-scenes content.',
                'priority'     => 'P2',
                'status'       => 'TODO',
                'due_date'     => '2026-04-26',
                'start_date'   => '2026-04-20',
                'department_id'=> $salesDept?->id,
                'created_by'   => $sales?->id,
                'category'     => 'marketing',
                'tags'         => ['social-media', 'content', 'marketing'],
                'assignees'    => [$sales?->id],
                'comments'     => [],
            ],
            [
                'task_number'  => 'TSK-007',
                'title'        => 'Pool Pump Maintenance & Filter Replacement',
                'description'  => 'Scheduled quarterly maintenance for main pool pump system. Replace filters, check chemical dosing equipment, and inspect pool heating unit.',
                'priority'     => 'P2',
                'status'       => 'TODO',
                'due_date'     => '2026-04-30',
                'start_date'   => '2026-04-28',
                'department_id'=> $maintDept?->id,
                'created_by'   => $maint?->id,
                'category'     => 'maintenance',
                'tags'         => ['pool', 'maintenance', 'quarterly'],
                'assignees'    => [$maint?->id],
                'comments'     => [],
            ],
            [
                'task_number'  => 'TSK-008',
                'title'        => 'Staff Performance Reviews — Q1 2026',
                'description'  => 'Conduct Q1 performance reviews for all department heads. Collect self-assessments, schedule 1:1 meetings, and submit final reports to GM.',
                'priority'     => 'P2',
                'status'       => 'IN_PROGRESS',
                'due_date'     => '2026-04-30',
                'start_date'   => '2026-04-15',
                'department_id'=> $hrDept?->id,
                'created_by'   => $gm?->id,
                'category'     => 'hr',
                'tags'         => ['performance', 'review', 'q1'],
                'assignees'    => [$hr?->id, $agm?->id],
                'comments'     => [
                    ['user_id' => $hr?->id, 'body' => '8 of 12 reviews completed.'],
                ],
            ],
            [
                'task_number'  => 'TSK-009',
                'title'        => 'Update Guest Welcome Pack',
                'description'  => 'Refresh in-room welcome pack content. Update local attraction guides, hotel service menu, and QR codes.',
                'priority'     => 'P3',
                'status'       => 'TODO',
                'due_date'     => '2026-05-30',
                'start_date'   => '2026-05-01',
                'department_id'=> $foDept?->id,
                'created_by'   => $fo?->id,
                'category'     => 'guest_experience',
                'tags'         => ['welcome-pack', 'guest-experience'],
                'assignees'    => [$fo?->id],
                'comments'     => [],
            ],
            [
                'task_number'  => 'TSK-010',
                'title'        => 'Annual Fire Safety Inspection Preparation',
                'description'  => 'Prepare for annual fire safety inspection. Test all fire alarms, extinguisher checks, and update evacuation plan signage throughout the property.',
                'priority'     => 'P1',
                'status'       => 'TODO',
                'due_date'     => '2026-05-10',
                'start_date'   => '2026-04-28',
                'department_id'=> $maintDept?->id,
                'created_by'   => $agm?->id,
                'category'     => 'safety',
                'tags'         => ['fire-safety', 'inspection', 'compliance'],
                'assignees'    => [$maint?->id],
                'comments'     => [],
            ],
            [
                'task_number'  => 'TSK-011',
                'title'        => 'Q1 Financial Report Submission',
                'description'  => 'Compile and submit Q1 financial report to ownership group including P&L, balance sheet, and cash flow statement.',
                'priority'     => 'P1',
                'status'       => 'DONE',
                'due_date'     => '2026-04-15',
                'start_date'   => '2026-04-01',
                'completed_at' => '2026-04-14 10:30:00',
                'department_id'=> $finDept?->id,
                'created_by'   => $gm?->id,
                'category'     => 'reporting',
                'tags'         => ['finance', 'quarterly', 'report'],
                'assignees'    => [$fin?->id],
                'comments'     => [
                    ['user_id' => $fin?->id, 'body' => 'Report submitted to owner group via email on April 14.'],
                    ['user_id' => $gm?->id,  'body' => 'Reviewed and approved. Good work team.'],
                ],
            ],
            [
                'task_number'  => 'TSK-012',
                'title'        => 'New Employee Orientation — March Batch',
                'description'  => 'Onboard 4 new employees hired in March. Conduct hotel tour, systems training, service standards workshop.',
                'priority'     => 'P2',
                'status'       => 'DONE',
                'due_date'     => '2026-03-31',
                'start_date'   => '2026-03-20',
                'completed_at' => '2026-03-28 17:00:00',
                'department_id'=> $hrDept?->id,
                'created_by'   => $hr?->id,
                'category'     => 'hr',
                'tags'         => ['onboarding', 'training'],
                'assignees'    => [$hr?->id],
                'comments'     => [
                    ['user_id' => $hr?->id, 'body' => 'All 4 employees completed orientation successfully.'],
                ],
            ],
            [
                'task_number'  => 'TSK-013',
                'title'        => 'Partnership Proposal — Siem Reap Tour Operators',
                'description'  => 'Draft and present partnership proposal to 3 key Siem Reap tour operators for room block bookings and activity packages.',
                'priority'     => 'P1',
                'status'       => 'IN_PROGRESS',
                'due_date'     => '2026-05-15',
                'start_date'   => '2026-04-10',
                'department_id'=> $salesDept?->id,
                'created_by'   => $gm?->id,
                'category'     => 'sales',
                'tags'         => ['partnership', 'b2b', 'bookings'],
                'assignees'    => [$sales?->id, $gm?->id],
                'comments'     => [
                    ['user_id' => $sales?->id, 'body' => 'Meeting scheduled with Angkor Discovery Tours for May 3.'],
                ],
            ],
            [
                'task_number'  => 'TSK-014',
                'title'        => 'Energy Audit & Cost Reduction Initiative',
                'description'  => 'Conduct comprehensive energy audit across all hotel facilities. Identify top 5 cost reduction opportunities and present ROI projections.',
                'priority'     => 'P2',
                'status'       => 'TODO',
                'due_date'     => '2026-05-31',
                'start_date'   => '2026-05-05',
                'department_id'=> $execDept?->id,
                'created_by'   => $agm?->id,
                'category'     => 'operations',
                'tags'         => ['energy', 'cost-reduction', 'sustainability'],
                'assignees'    => [$maint?->id, $fin?->id],
                'comments'     => [],
            ],
        ];

        foreach ($tasks as $taskData) {
            $assignees = $taskData['assignees'] ?? [];
            $comments  = $taskData['comments'] ?? [];
            $tagsVal   = $taskData['tags'] ?? [];
            unset($taskData['assignees'], $taskData['comments']);

            $taskData['tags'] = $tagsVal;

            $task = Task::firstOrCreate(
                ['task_number' => $taskData['task_number']],
                $taskData
            );

            $validAssignees = array_filter($assignees);
            if (!empty($validAssignees)) {
                $task->assignees()->syncWithoutDetaching($validAssignees);
            }

            foreach ($comments as $comment) {
                if (!$comment['user_id']) continue;
                TaskComment::firstOrCreate(
                    ['task_id' => $task->id, 'user_id' => $comment['user_id'], 'body' => $comment['body']],
                    ['mentions' => json_encode([])]
                );
            }
        }
    }
}
