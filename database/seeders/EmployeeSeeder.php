<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        // Distribute 40 employees across the existing companies so the
        // listing has enough rows to demonstrate pagination.
        Employee::factory()
            ->count(40)
            ->recycle($companies)
            ->create();
    }
}
