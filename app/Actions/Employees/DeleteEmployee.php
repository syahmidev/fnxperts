<?php

namespace App\Actions\Employees;

use App\Models\Employee;

final class DeleteEmployee
{
    public function handle(Employee $employee): void
    {
        $employee->delete();
    }
}
