<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            UserSeeder::class,
            KpiSeeder::class,
            TaskSeeder::class,
            FinancialSeeder::class,
            ActionPlanSeeder::class,
            DocumentSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
