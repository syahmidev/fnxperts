<?php

namespace App\Http\Controllers;

use App\Actions\Employees\CreateEmployee;
use App\Actions\Employees\DeleteEmployee;
use App\Actions\Employees\UpdateEmployee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $employees = Employee::query()
            ->with('company')
            ->latest()
            ->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create(): View
    {
        return view('employees.create', [
            'companies' => Company::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreEmployeeRequest $request, CreateEmployee $action): RedirectResponse
    {
        $employee = $action->handle($request->validated());

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee): View
    {
        $employee->load('company');

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        return view('employees.edit', [
            'employee' => $employee,
            'companies' => Company::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee, UpdateEmployee $action): RedirectResponse
    {
        $employee = $action->handle($employee, $request->validated());

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee, DeleteEmployee $action): RedirectResponse
    {
        $action->handle($employee);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
