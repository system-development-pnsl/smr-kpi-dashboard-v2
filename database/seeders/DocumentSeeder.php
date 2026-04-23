<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $fin    = User::where('email', 'finance@smrhotel.com')->first();
        $gm     = User::where('email', 'gm@smrhotel.com')->first();
        $hr     = User::where('email', 'hr@smrhotel.com')->first();
        $fnb    = User::where('email', 'fnb.manager@smrhotel.com')->first();

        $finDept  = Department::where('code', 'FIN')->first();
        $hrDept   = Department::where('code', 'HR')->first();
        $fnbDept  = Department::where('code', 'FNB')->first();
        $execDept = Department::where('code', 'EXEC')->first();

        $documents = [
            // Confirmed document with extracted data
            [
                'original_name'  => 'Q1-2026-Financial-Report.pdf',
                'stored_path'    => 'documents/q1-2026-financial-report.pdf',
                'mime_type'      => 'application/pdf',
                'size_bytes'     => 524288,
                'file_type'      => 'PDF',
                'sha256'         => sha1('q1-2026-financial-report'),
                'department_id'  => $finDept?->id,
                'description'    => 'Q1 2026 Financial Report — Comprehensive P&L, balance sheet, and cash flow statement.',
                'uploaded_by'    => $fin?->id,
                'ai_status'      => 'confirmed',
                'confirmed_at'   => '2026-04-14 11:00:00',
                'confirmed_by'   => $gm?->id,
                'extracted_data' => [
                    'document_summary' => 'Q1 2026 Financial Report for Sun & Moon Riverside Hotel. Total revenue of $489,200 against budget of $470,000 (+4.1%). GOP margin achieved 32.1%, up from 28.5% in Q1 2025. Payroll cost ratio improved to 35.2%. All key financial targets on track for full year.',
                    'extracted_fields' => [
                        ['field_name' => 'Total Revenue Q1',       'field_key' => 'total_revenue_q1',    'value' => 489200,  'unit' => 'USD', 'period' => '2026-01-01', 'target_module' => 'financial', 'confidence' => 0.97],
                        ['field_name' => 'GOP Margin Q1',          'field_key' => 'gop_margin_q1',       'value' => 32.1,    'unit' => '%',   'period' => '2026-01-01', 'target_module' => 'kpi',       'confidence' => 0.95],
                        ['field_name' => 'Room Revenue Q1',        'field_key' => 'room_revenue_q1',     'value' => 365200,  'unit' => 'USD', 'period' => '2026-01-01', 'target_module' => 'financial', 'confidence' => 0.98],
                        ['field_name' => 'F&B Revenue Q1',         'field_key' => 'fnb_revenue_q1',      'value' => 124000,  'unit' => 'USD', 'period' => '2026-01-01', 'target_module' => 'financial', 'confidence' => 0.96],
                        ['field_name' => 'Payroll Cost Ratio',     'field_key' => 'payroll_cost_ratio',  'value' => 35.2,    'unit' => '%',   'period' => '2026-01-01', 'target_module' => 'kpi',       'confidence' => 0.94],
                        ['field_name' => 'Average Occupancy Q1',   'field_key' => 'avg_occupancy_q1',    'value' => 77.9,    'unit' => '%',   'period' => '2026-01-01', 'target_module' => 'kpi',       'confidence' => 0.93],
                    ],
                    'unrecognized_items' => [],
                ],
            ],

            // Extracted / review status
            [
                'original_name'  => 'April-Occupancy-Summary.xlsx',
                'stored_path'    => 'documents/april-occupancy-summary.xlsx',
                'mime_type'      => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'size_bytes'     => 65536,
                'file_type'      => 'XLSX',
                'sha256'         => sha1('april-occupancy-summary'),
                'department_id'  => $finDept?->id,
                'description'    => 'Front Office April occupancy summary — daily breakdown with ADR and RevPAR.',
                'uploaded_by'    => $fin?->id,
                'ai_status'      => 'review',
                'extracted_data' => [
                    'document_summary' => 'April 2026 occupancy summary for Sun & Moon Riverside Hotel. Average occupancy 85.3%, up from 78.2% in March. ADR $99.20 (+$2.45 vs March). RevPAR $84.57, exceeding Q2 target of $76.',
                    'extracted_fields' => [
                        ['field_name' => 'April Occupancy Rate',  'field_key' => 'occupancy_april_2026', 'value' => 85.3,  'unit' => '%',   'period' => '2026-04-01', 'target_module' => 'kpi',       'confidence' => 0.96],
                        ['field_name' => 'April ADR',             'field_key' => 'adr_april_2026',       'value' => 99.20, 'unit' => 'USD', 'period' => '2026-04-01', 'target_module' => 'kpi',       'confidence' => 0.95],
                        ['field_name' => 'April RevPAR',          'field_key' => 'revpar_april_2026',    'value' => 84.57, 'unit' => 'USD', 'period' => '2026-04-01', 'target_module' => 'kpi',       'confidence' => 0.94],
                        ['field_name' => 'Total Room Revenue Apr','field_key' => 'room_revenue_apr',     'value' => 114200,'unit' => 'USD', 'period' => '2026-04-01', 'target_module' => 'financial', 'confidence' => 0.88],
                        ['field_name' => 'Walk-in Bookings',      'field_key' => 'walkin_bookings_apr',  'value' => 42,    'unit' => 'rooms','period' => '2026-04-01','target_module' => 'none',      'confidence' => 0.72],
                    ],
                    'unrecognized_items' => ['Channel mix breakdown (OTA vs Direct vs Walk-in) partially illegible'],
                ],
            ],

            // HR document — confirmed
            [
                'original_name'  => 'Q1-HR-Metrics-Report.pdf',
                'stored_path'    => 'documents/q1-hr-metrics-report.pdf',
                'mime_type'      => 'application/pdf',
                'size_bytes'     => 245760,
                'file_type'      => 'PDF',
                'sha256'         => sha1('q1-hr-metrics-report'),
                'department_id'  => $hrDept?->id,
                'description'    => 'Q1 2026 HR metrics — headcount, turnover, training hours, and recruitment pipeline.',
                'uploaded_by'    => $hr?->id,
                'ai_status'      => 'confirmed',
                'confirmed_at'   => '2026-04-10 15:30:00',
                'confirmed_by'   => $gm?->id,
                'extracted_data' => [
                    'document_summary' => 'Q1 2026 HR Metrics Report. Total headcount 87 (target 90). Staff turnover rate 18% vs 22.5% in Q4 2025, showing improvement. Training completion rate 78%. 4 new hires completed orientation. Sick leave rate 3.2%, within acceptable range.',
                    'extracted_fields' => [
                        ['field_name' => 'Total Headcount',       'field_key' => 'headcount_q1',         'value' => 87,    'unit' => 'staff', 'period' => '2026-01-01', 'target_module' => 'kpi',  'confidence' => 0.98],
                        ['field_name' => 'Staff Turnover Rate',   'field_key' => 'turnover_rate_q1',     'value' => 18.0,  'unit' => '%',     'period' => '2026-01-01', 'target_module' => 'kpi',  'confidence' => 0.95],
                        ['field_name' => 'Training Completion',   'field_key' => 'training_completion',  'value' => 78.0,  'unit' => '%',     'period' => '2026-01-01', 'target_module' => 'kpi',  'confidence' => 0.91],
                        ['field_name' => 'New Hires Q1',          'field_key' => 'new_hires_q1',         'value' => 4,     'unit' => 'staff', 'period' => '2026-01-01', 'target_module' => 'none', 'confidence' => 0.99],
                        ['field_name' => 'Sick Leave Rate',       'field_key' => 'sick_leave_rate',      'value' => 3.2,   'unit' => '%',     'period' => '2026-01-01', 'target_module' => 'none', 'confidence' => 0.90],
                    ],
                    'unrecognized_items' => [],
                ],
            ],

            // F&B weekly report — pending AI extraction
            [
                'original_name'  => 'FNB-Weekly-Sales-W16-2026.pdf',
                'stored_path'    => 'documents/fnb-weekly-sales-w16-2026.pdf',
                'mime_type'      => 'application/pdf',
                'size_bytes'     => 102400,
                'file_type'      => 'PDF',
                'sha256'         => sha1('fnb-weekly-sales-w16-2026'),
                'department_id'  => $fnbDept?->id,
                'description'    => 'F&B weekly sales report — Week 16, April 15-21 2026.',
                'uploaded_by'    => $fnb?->id,
                'ai_status'      => 'pending',
                'extracted_data' => null,
            ],

            // Executive strategic plan — processing
            [
                'original_name'  => 'Strategic-Plan-2026-Full.pdf',
                'stored_path'    => 'documents/strategic-plan-2026-full.pdf',
                'mime_type'      => 'application/pdf',
                'size_bytes'     => 1048576,
                'file_type'      => 'PDF',
                'sha256'         => sha1('strategic-plan-2026-full'),
                'department_id'  => $execDept?->id,
                'description'    => 'Full strategic plan for Sun & Moon Riverside Hotel 2026 — vision, goals, and KPI targets.',
                'uploaded_by'    => $gm?->id,
                'ai_status'      => 'pending',
                'extracted_data' => null,
            ],

            // Failed extraction
            [
                'original_name'  => 'Scanned-Invoice-March.jpg',
                'stored_path'    => 'documents/scanned-invoice-march.jpg',
                'mime_type'      => 'image/jpeg',
                'size_bytes'     => 2097152,
                'file_type'      => 'JPG',
                'sha256'         => sha1('scanned-invoice-march'),
                'department_id'  => $finDept?->id,
                'description'    => 'Scanned supplier invoice — March 2026.',
                'uploaded_by'    => $fin?->id,
                'ai_status'      => 'failed',
                'extracted_data' => null,
            ],
        ];

        foreach ($documents as $doc) {
            Document::firstOrCreate(
                ['sha256' => $doc['sha256']],
                $doc
            );
        }
    }
}
