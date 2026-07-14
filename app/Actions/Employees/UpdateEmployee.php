<?php

namespace App\Actions\Employees;

use App\Models\Employee;

final class UpdateEmployee
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Employee $employee, array $attributes): Employee
    {
        $employee->update($attributes);

        return $employee->refresh();
    }
}
