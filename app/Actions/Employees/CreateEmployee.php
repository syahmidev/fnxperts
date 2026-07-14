<?php

namespace App\Actions\Employees;

use App\Models\Employee;

final class CreateEmployee
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(array $attributes): Employee
    {
        return Employee::query()->create($attributes);
    }
}
