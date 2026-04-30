<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Kpi;
use App\Models\KpiEntry;
use App\Models\KpiTarget;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KpiSeeder extends Seeder
{
    public function run(): void
    {
        $submitter = User::where('email', 'gm@sunmoon.hotel')->first();
        $setter    = User::where('email', 'finance@smrhotel.com')->first();

        $fo    = Department::where('code', 'FO')->first();
        $fnb   = Department::where('code', 'FNB')->first();
        $hk    = Department::where('code', 'HK')->first();
        $fin   = Department::where('code', 'FIN')->first();
        $sales = Department::where('code', 'SALES')->first();
        $hr    = Department::where('code', 'HR')->first();
        $spa   = Department::where('code', 'SPA')->first();

        $kpis = [
            // ── Front Office ──────────────────────────────────────
            [
                'name'             => 'Occupancy Rate',
                'slug'             => 'occupancy-rate',
                'department_id'    => $fo?->id,
                'unit'             => '%',
                'target'           => 80,
                'is_lower_better'  => false,
                'update_frequency' => 'daily',
                'description'      => 'Percentage of available rooms occupied per night.',
                'is_active'        => true,
                'is_hotel_wide'    => true,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 74.5],
                    ['period' => '2026-02-01', 'value' => 78.2],
                    ['period' => '2026-03-01', 'value' => 81.0],
                    ['period' => '2026-04-01', 'value' => 85.3],
                ],
            ],
            [
                'name'             => 'Average Daily Rate (ADR)',
                'slug'             => 'average-daily-rate',
                'department_id'    => $fo?->id,
                'unit'             => 'USD',
                'target'           => 95,
                'is_lower_better'  => false,
                'update_frequency' => 'monthly',
                'description'      => 'Average revenue per rented room per day.',
                'is_active'        => true,
                'is_hotel_wide'    => true,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 88.50],
                    ['period' => '2026-02-01', 'value' => 91.00],
                    ['period' => '2026-03-01', 'value' => 96.75],
                    ['period' => '2026-04-01', 'value' => 99.20],
                ],
            ],
            [
                'name'             => 'RevPAR',
                'slug'             => 'revpar',
                'department_id'    => $fo?->id,
                'unit'             => 'USD',
                'target'           => 76,
                'is_lower_better'  => false,
                'update_frequency' => 'monthly',
                'description'      => 'Revenue Per Available Room (ADR × Occupancy).',
                'is_active'        => true,
                'is_hotel_wide'    => true,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 65.90],
                    ['period' => '2026-02-01', 'value' => 71.10],
                    ['period' => '2026-03-01', 'value' => 78.36],
                    ['period' => '2026-04-01', 'value' => 84.57],
                ],
            ],
            [
                'name'             => 'Guest Satisfaction Score',
                'slug'             => 'guest-satisfaction',
                'department_id'    => $fo?->id,
                'unit'             => '/10',
                'target'           => 8.5,
                'is_lower_better'  => false,
                'update_frequency' => 'monthly',
                'description'      => 'Average guest satisfaction rating from post-stay surveys.',
                'is_active'        => true,
                'is_hotel_wide'    => false,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 7.9],
                    ['period' => '2026-02-01', 'value' => 8.2],
                    ['period' => '2026-03-01', 'value' => 8.6],
                    ['period' => '2026-04-01', 'value' => 8.7],
                ],
            ],
            [
                'name'             => 'Check-in Wait Time',
                'slug'             => 'checkin-wait-time',
                'department_id'    => $fo?->id,
                'unit'             => 'min',
                'target'           => 5,
                'is_lower_better'  => true,
                'update_frequency' => 'weekly',
                'description'      => 'Average check-in wait time in minutes.',
                'is_active'        => true,
                'is_hotel_wide'    => false,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 8.5],
                    ['period' => '2026-02-01', 'value' => 7.0],
                    ['period' => '2026-03-01', 'value' => 5.5],
                    ['period' => '2026-04-01', 'value' => 4.8],
                ],
            ],

            // ── F&B ──────────────────────────────────────────────
            [
                'name'             => 'F&B Revenue',
                'slug'             => 'fnb-revenue',
                'department_id'    => $fnb?->id,
                'unit'             => 'USD',
                'target'           => 45000,
                'is_lower_better'  => false,
                'update_frequency' => 'monthly',
                'description'      => 'Total Food & Beverage revenue per month.',
                'is_active'        => true,
                'is_hotel_wide'    => false,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 38200],
                    ['period' => '2026-02-01', 'value' => 41500],
                    ['period' => '2026-03-01', 'value' => 46800],
                    ['period' => '2026-04-01', 'value' => 49200],
                ],
            ],
            [
                'name'             => 'Food Cost %',
                'slug'             => 'food-cost-pct',
                'department_id'    => $fnb?->id,
                'unit'             => '%',
                'target'           => 32,
                'is_lower_better'  => true,
                'update_frequency' => 'monthly',
                'description'      => 'Food cost as a percentage of food revenue.',
                'is_active'        => true,
                'is_hotel_wide'    => false,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 35.2],
                    ['period' => '2026-02-01', 'value' => 33.8],
                    ['period' => '2026-03-01', 'value' => 31.5],
                    ['period' => '2026-04-01', 'value' => 30.9],
                ],
            ],
            [
                'name'             => 'Restaurant Covers per Day',
                'slug'             => 'restaurant-covers',
                'department_id'    => $fnb?->id,
                'unit'             => 'covers',
                'target'           => 80,
                'is_lower_better'  => false,
                'update_frequency' => 'daily',
                'description'      => 'Average number of restaurant guests served per day.',
                'is_active'        => true,
                'is_hotel_wide'    => false,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 62],
                    ['period' => '2026-02-01', 'value' => 71],
                    ['period' => '2026-03-01', 'value' => 83],
                    ['period' => '2026-04-01', 'value' => 90],
                ],
            ],

            // ── Housekeeping ─────────────────────────────────────
            [
                'name'             => 'Room Cleanliness Score',
                'slug'             => 'room-cleanliness',
                'department_id'    => $hk?->id,
                'unit'             => '/10',
                'target'           => 9.0,
                'is_lower_better'  => false,
                'update_frequency' => 'monthly',
                'description'      => 'Average room cleanliness score from guest feedback.',
                'is_active'        => true,
                'is_hotel_wide'    => false,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 8.6],
                    ['period' => '2026-02-01', 'value' => 8.8],
                    ['period' => '2026-03-01', 'value' => 9.1],
                    ['period' => '2026-04-01', 'value' => 9.3],
                ],
            ],
            [
                'name'             => 'Rooms Cleaned per Attendant',
                'slug'             => 'rooms-per-attendant',
                'department_id'    => $hk?->id,
                'unit'             => 'rooms',
                'target'           => 14,
                'is_lower_better'  => false,
                'update_frequency' => 'weekly',
                'description'      => 'Average number of rooms cleaned per attendant per shift.',
                'is_active'        => true,
                'is_hotel_wide'    => false,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 12],
                    ['period' => '2026-02-01', 'value' => 13],
                    ['period' => '2026-03-01', 'value' => 14],
                    ['period' => '2026-04-01', 'value' => 15],
                ],
            ],

            // ── Finance ──────────────────────────────────────────
            [
                'name'             => 'Total Revenue',
                'slug'             => 'total-revenue',
                'department_id'    => $fin?->id,
                'unit'             => 'USD',
                'target'           => 180000,
                'is_lower_better'  => false,
                'update_frequency' => 'monthly',
                'description'      => 'Total hotel revenue from all sources.',
                'is_active'        => true,
                'is_hotel_wide'    => true,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 148500],
                    ['period' => '2026-02-01', 'value' => 162000],
                    ['period' => '2026-03-01', 'value' => 178500],
                    ['period' => '2026-04-01', 'value' => 194200],
                ],
            ],
            [
                'name'             => 'GOP Margin',
                'slug'             => 'gop-margin',
                'department_id'    => $fin?->id,
                'unit'             => '%',
                'target'           => 35,
                'is_lower_better'  => false,
                'update_frequency' => 'monthly',
                'description'      => 'Gross Operating Profit as a percentage of total revenue.',
                'is_active'        => true,
                'is_hotel_wide'    => true,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 29.5],
                    ['period' => '2026-02-01', 'value' => 32.1],
                    ['period' => '2026-03-01', 'value' => 36.4],
                    ['period' => '2026-04-01', 'value' => 38.2],
                ],
            ],
            [
                'name'             => 'Operating Cost Ratio',
                'slug'             => 'operating-cost-ratio',
                'department_id'    => $fin?->id,
                'unit'             => '%',
                'target'           => 60,
                'is_lower_better'  => true,
                'update_frequency' => 'monthly',
                'description'      => 'Total operating costs as a percentage of revenue.',
                'is_active'        => true,
                'is_hotel_wide'    => false,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 67.2],
                    ['period' => '2026-02-01', 'value' => 64.8],
                    ['period' => '2026-03-01', 'value' => 61.0],
                    ['period' => '2026-04-01', 'value' => 58.5],
                ],
            ],

            // ── Sales & Marketing ────────────────────────────────
            [
                'name'             => 'Direct Booking Rate',
                'slug'             => 'direct-booking-rate',
                'department_id'    => $sales?->id,
                'unit'             => '%',
                'target'           => 40,
                'is_lower_better'  => false,
                'update_frequency' => 'monthly',
                'description'      => 'Percentage of bookings made directly (not via OTA).',
                'is_active'        => true,
                'is_hotel_wide'    => false,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 28.5],
                    ['period' => '2026-02-01', 'value' => 31.0],
                    ['period' => '2026-03-01', 'value' => 36.5],
                    ['period' => '2026-04-01', 'value' => 41.2],
                ],
            ],
            [
                'name'             => 'OTA Commission %',
                'slug'             => 'ota-commission-pct',
                'department_id'    => $sales?->id,
                'unit'             => '%',
                'target'           => 15,
                'is_lower_better'  => true,
                'update_frequency' => 'monthly',
                'description'      => 'Average OTA commission as a percentage of booking value.',
                'is_active'        => true,
                'is_hotel_wide'    => false,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 19.5],
                    ['period' => '2026-02-01', 'value' => 18.2],
                    ['period' => '2026-03-01', 'value' => 16.8],
                    ['period' => '2026-04-01', 'value' => 14.9],
                ],
            ],
            [
                'name'             => 'Repeat Guest Rate',
                'slug'             => 'repeat-guest-rate',
                'department_id'    => $sales?->id,
                'unit'             => '%',
                'target'           => 30,
                'is_lower_better'  => false,
                'update_frequency' => 'monthly',
                'description'      => 'Percentage of guests who have stayed before.',
                'is_active'        => true,
                'is_hotel_wide'    => true,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 22.0],
                    ['period' => '2026-02-01', 'value' => 25.5],
                    ['period' => '2026-03-01', 'value' => 28.0],
                    ['period' => '2026-04-01', 'value' => 31.5],
                ],
            ],

            // ── HR ───────────────────────────────────────────────
            [
                'name'             => 'Staff Turnover Rate',
                'slug'             => 'staff-turnover',
                'department_id'    => $hr?->id,
                'unit'             => '%',
                'target'           => 15,
                'is_lower_better'  => true,
                'update_frequency' => 'monthly',
                'description'      => 'Annual staff turnover rate.',
                'is_active'        => true,
                'is_hotel_wide'    => true,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 22.5],
                    ['period' => '2026-02-01', 'value' => 20.0],
                    ['period' => '2026-03-01', 'value' => 18.0],
                    ['period' => '2026-04-01', 'value' => 16.0],
                ],
            ],
            [
                'name'             => 'Training Hours per Staff',
                'slug'             => 'training-hours',
                'department_id'    => $hr?->id,
                'unit'             => 'hrs',
                'target'           => 20,
                'is_lower_better'  => false,
                'update_frequency' => 'monthly',
                'description'      => 'Average training hours per staff per quarter.',
                'is_active'        => true,
                'is_hotel_wide'    => false,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 12],
                    ['period' => '2026-02-01', 'value' => 15],
                    ['period' => '2026-03-01', 'value' => 18],
                    ['period' => '2026-04-01', 'value' => 22],
                ],
            ],

            // ── Spa ──────────────────────────────────────────────
            [
                'name'             => 'Spa Revenue',
                'slug'             => 'spa-revenue',
                'department_id'    => $spa?->id,
                'unit'             => 'USD',
                'target'           => 12000,
                'is_lower_better'  => false,
                'update_frequency' => 'monthly',
                'description'      => 'Total spa and wellness revenue per month.',
                'is_active'        => true,
                'is_hotel_wide'    => false,
                'entries' => [
                    ['period' => '2026-01-01', 'value' => 9500],
                    ['period' => '2026-02-01', 'value' => 10800],
                    ['period' => '2026-03-01', 'value' => 12500],
                    ['period' => '2026-04-01', 'value' => 13800],
                ],
            ],
        ];

        foreach ($kpis as $kpiData) {
            $entries = $kpiData['entries'] ?? [];
            unset($kpiData['entries']);

            $kpi = Kpi::firstOrCreate(['slug' => $kpiData['slug']], $kpiData);

            foreach ($entries as $entry) {
                KpiEntry::firstOrCreate(
                    ['kpi_id' => $kpi->id, 'period' => $entry['period']],
                    [
                        'value'        => $entry['value'],
                        'note'         => 'Monthly aggregated data.',
                        'source'       => 'manual',
                        'submitted_by' => $submitter?->id,
                    ]
                );

                // Set a target for each period
                KpiTarget::firstOrCreate(
                    ['kpi_id' => $kpi->id, 'period' => $entry['period']],
                    [
                        'target_value' => $kpi->target,
                        'set_by'       => $setter?->id,
                        'approved_by'  => $setter?->id,
                        'approved_at'  => now(),
                        'notes'        => 'Standard annual target.',
                    ]
                );
            }
        }
    }
}
