<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['code' => 'EXEC',  'label' => 'Executive',           'color' => '#111827', 'sort_order' => 1],
            ['code' => 'FNB',   'label' => 'Food & Beverage',      'color' => '#d97706', 'sort_order' => 2],
            ['code' => 'FO',    'label' => 'Front Office',         'color' => '#2563eb', 'sort_order' => 3],
            ['code' => 'HK',    'label' => 'Housekeeping',         'color' => '#7c3aed', 'sort_order' => 4],
            ['code' => 'FIN',   'label' => 'Finance',              'color' => '#059669', 'sort_order' => 5],
            ['code' => 'HR',    'label' => 'Human Resources',      'color' => '#db2777', 'sort_order' => 6],
            ['code' => 'MAINT', 'label' => 'Maintenance',          'color' => '#9f1239', 'sort_order' => 7],
            ['code' => 'SPA',   'label' => 'Spa & Wellness',       'color' => '#0e7490', 'sort_order' => 8],
            ['code' => 'SALES', 'label' => 'Sales & Marketing',    'color' => '#ea580c', 'sort_order' => 9],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], array_merge($dept, ['is_active' => true]));
        }
    }
}
