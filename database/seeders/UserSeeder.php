<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $exec  = Department::where('code', 'EXEC')->first();
        $fin   = Department::where('code', 'FIN')->first();
        $hr    = Department::where('code', 'HR')->first();
        $fo    = Department::where('code', 'FO')->first();
        $fnb   = Department::where('code', 'FNB')->first();
        $hk    = Department::where('code', 'HK')->first();
        $sales = Department::where('code', 'SALES')->first();
        $maint = Department::where('code', 'MAINT')->first();
        $spa   = Department::where('code', 'SPA')->first();

        $users = [
            // ── Executives ──────────────────────────────────────
            [
                'code'            => 'EMP001',
                'full_name'       => 'Sopheak Chan',
                'full_name_km'    => 'ចាន់ សុភ័ក្ត',
                'email'           => 'owner@smrhotel.com',
                'password'        => Hash::make('password'),
                'gender'          => 'male',
                'phone'           => '+855 12 345 678',
                'job_title'       => 'Owner',
                'department_id'   => $exec?->id,
                'role'            => 'owner',
                'start_date'      => '2015-01-01',
                'access_modules'  => json_encode(['kpi', 'tasks', 'financial', 'documents', 'reports', 'hr']),
                'employment_type' => 'full_time',
                'work_schedule'   => 'office_hours',
                'status'          => 'active',
            ],
            [
                'code'            => 'EMP002',
                'full_name'       => 'Virak Lim',
                'full_name_km'    => 'លីម វីរៈ',
                'email'           => 'gm@smrhotel.com',
                'password'        => Hash::make('password'),
                'gender'          => 'male',
                'phone'           => '+855 12 456 789',
                'job_title'       => 'General Manager',
                'department_id'   => $exec?->id,
                'role'            => 'general_manager',
                'start_date'      => '2018-03-15',
                'access_modules'  => json_encode(['kpi', 'tasks', 'financial', 'documents', 'reports', 'hr']),
                'employment_type' => 'full_time',
                'work_schedule'   => 'office_hours',
                'status'          => 'active',
            ],
            [
                'code'            => 'EMP003',
                'full_name'       => 'Maly Prak',
                'full_name_km'    => 'ប្រាក់ មាលី',
                'email'           => 'agm@smrhotel.com',
                'password'        => Hash::make('password'),
                'gender'          => 'female',
                'phone'           => '+855 12 567 890',
                'job_title'       => 'Assistant General Manager',
                'department_id'   => $exec?->id,
                'role'            => 'agm',
                'start_date'      => '2019-06-01',
                'access_modules'  => json_encode(['kpi', 'tasks', 'financial', 'documents', 'reports']),
                'employment_type' => 'full_time',
                'work_schedule'   => 'office_hours',
                'status'          => 'active',
            ],

            // ── Finance ─────────────────────────────────────────
            [
                'code'            => 'EMP004',
                'full_name'       => 'Dara Noun',
                'full_name_km'    => 'នួន ដារ៉ា',
                'email'           => 'finance@smrhotel.com',
                'password'        => Hash::make('password'),
                'gender'          => 'male',
                'phone'           => '+855 12 678 901',
                'job_title'       => 'Finance Director',
                'department_id'   => $fin?->id,
                'role'            => 'finance_director',
                'start_date'      => '2017-09-01',
                'access_modules'  => json_encode(['kpi', 'tasks', 'financial', 'documents', 'reports']),
                'employment_type' => 'full_time',
                'work_schedule'   => 'office_hours',
                'status'          => 'active',
            ],
            [
                'code'            => 'EMP005',
                'full_name'       => 'Sreyleak Keo',
                'full_name_km'    => 'កែវ ស្រីលាក់',
                'email'           => 'accountant@smrhotel.com',
                'password'        => Hash::make('password'),
                'gender'          => 'female',
                'phone'           => '+855 12 789 012',
                'job_title'       => 'Senior Accountant',
                'department_id'   => $fin?->id,
                'role'            => 'staff',
                'start_date'      => '2020-01-15',
                'access_modules'  => json_encode(['tasks', 'financial', 'documents']),
                'employment_type' => 'full_time',
                'work_schedule'   => 'office_hours',
                'status'          => 'active',
            ],

            // ── HR ──────────────────────────────────────────────
            [
                'code'            => 'EMP006',
                'full_name'       => 'Bopha Sok',
                'full_name_km'    => 'សុក បុប្ផា',
                'email'           => 'hr@smrhotel.com',
                'password'        => Hash::make('password'),
                'gender'          => 'female',
                'phone'           => '+855 12 890 123',
                'job_title'       => 'HR Manager',
                'department_id'   => $hr?->id,
                'role'            => 'head_of_dept',
                'start_date'      => '2019-03-01',
                'access_modules'  => json_encode(['kpi', 'tasks', 'documents', 'hr']),
                'employment_type' => 'full_time',
                'work_schedule'   => 'office_hours',
                'status'          => 'active',
            ],

            // ── Front Office ─────────────────────────────────────
            [
                'code'            => 'EMP007',
                'full_name'       => 'Rithy Heng',
                'full_name_km'    => 'ហេង រិទ្ធី',
                'email'           => 'fo.manager@smrhotel.com',
                'password'        => Hash::make('password'),
                'gender'          => 'male',
                'phone'           => '+855 12 901 234',
                'job_title'       => 'Front Office Manager',
                'department_id'   => $fo?->id,
                'role'            => 'head_of_dept',
                'start_date'      => '2018-07-01',
                'access_modules'  => json_encode(['kpi', 'tasks', 'documents']),
                'employment_type' => 'full_time',
                'work_schedule'   => 'office_hours',
                'status'          => 'active',
            ],
            [
                'code'            => 'EMP008',
                'full_name'       => 'Chanthy Ros',
                'full_name_km'    => 'រស់ ចាន់ធី',
                'email'           => 'reception@smrhotel.com',
                'password'        => Hash::make('password'),
                'gender'          => 'female',
                'phone'           => '+855 12 012 345',
                'job_title'       => 'Senior Receptionist',
                'department_id'   => $fo?->id,
                'role'            => 'staff',
                'start_date'      => '2021-02-01',
                'access_modules'  => json_encode(['tasks']),
                'employment_type' => 'full_time',
                'work_schedule'   => 'shift',
                'status'          => 'active',
            ],

            // ── F&B ──────────────────────────────────────────────
            [
                'code'            => 'EMP009',
                'full_name'       => 'Piseth Teng',
                'full_name_km'    => 'តេង ពិសិទ្ធ',
                'email'           => 'fnb.manager@smrhotel.com',
                'password'        => Hash::make('password'),
                'gender'          => 'male',
                'phone'           => '+855 12 123 456',
                'job_title'       => 'F&B Manager',
                'department_id'   => $fnb?->id,
                'role'            => 'head_of_dept',
                'start_date'      => '2019-11-01',
                'access_modules'  => json_encode(['kpi', 'tasks', 'documents']),
                'employment_type' => 'full_time',
                'work_schedule'   => 'office_hours',
                'status'          => 'active',
            ],

            // ── Housekeeping ─────────────────────────────────────
            [
                'code'            => 'EMP010',
                'full_name'       => 'Sreymom Yun',
                'full_name_km'    => 'យូន ស្រីម៉ម',
                'email'           => 'hk.manager@smrhotel.com',
                'password'        => Hash::make('password'),
                'gender'          => 'female',
                'phone'           => '+855 12 234 567',
                'job_title'       => 'Housekeeping Manager',
                'department_id'   => $hk?->id,
                'role'            => 'head_of_dept',
                'start_date'      => '2020-04-15',
                'access_modules'  => json_encode(['kpi', 'tasks']),
                'employment_type' => 'full_time',
                'work_schedule'   => 'office_hours',
                'status'          => 'active',
            ],

            // ── Sales & Marketing ────────────────────────────────
            [
                'code'            => 'EMP011',
                'full_name'       => 'Kimlong Sar',
                'full_name_km'    => 'សរ គឹមឡុង',
                'email'           => 'sales@smrhotel.com',
                'password'        => Hash::make('password'),
                'gender'          => 'male',
                'phone'           => '+855 12 345 678',
                'job_title'       => 'Sales & Marketing Manager',
                'department_id'   => $sales?->id,
                'role'            => 'head_of_dept',
                'start_date'      => '2021-01-10',
                'access_modules'  => json_encode(['kpi', 'tasks', 'documents']),
                'employment_type' => 'full_time',
                'work_schedule'   => 'office_hours',
                'status'          => 'active',
            ],

            // ── Maintenance ──────────────────────────────────────
            [
                'code'            => 'EMP012',
                'full_name'       => 'Sokha Mao',
                'full_name_km'    => 'មៅ សុខា',
                'email'           => 'maintenance@smrhotel.com',
                'password'        => Hash::make('password'),
                'gender'          => 'male',
                'phone'           => '+855 12 456 789',
                'job_title'       => 'Chief Engineer',
                'department_id'   => $maint?->id,
                'role'            => 'head_of_dept',
                'start_date'      => '2016-05-01',
                'access_modules'  => json_encode(['tasks', 'documents']),
                'employment_type' => 'full_time',
                'work_schedule'   => 'office_hours',
                'status'          => 'active',
            ],

            // ── Spa ─────────────────────────────────────────────
            [
                'code'            => 'EMP013',
                'full_name'       => 'Ratana Chea',
                'full_name_km'    => 'ជា រតនា',
                'email'           => 'spa@smrhotel.com',
                'password'        => Hash::make('password'),
                'gender'          => 'female',
                'phone'           => '+855 12 567 890',
                'job_title'       => 'Spa Manager',
                'department_id'   => $spa?->id,
                'role'            => 'head_of_dept',
                'start_date'      => '2022-01-01',
                'access_modules'  => json_encode(['kpi', 'tasks']),
                'employment_type' => 'full_time',
                'work_schedule'   => 'office_hours',
                'status'          => 'active',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }

        // Set reporting managers
        $gm    = User::where('email', 'gm@smrhotel.com')->first();
        $owner = User::where('email', 'owner@smrhotel.com')->first();

        if ($gm && $owner) {
            User::where('role', 'general_manager')->update(['reporting_manager_id' => $owner->id]);
            User::where('role', 'agm')->update(['reporting_manager_id' => $gm->id]);
            User::whereIn('role', ['head_of_dept', 'finance_director'])->update(['reporting_manager_id' => $gm->id]);
            User::where('role', 'staff')->update(['reporting_manager_id' => $gm->id]);
        }
    }
}
