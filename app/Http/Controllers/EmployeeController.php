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
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString() ?: null;

        $employees = Employee::query()
            ->search($search)
            ->with('company')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('employees.index', compact('employees', 'search'));
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

    public function trashed(): View
    {
        $employees = Employee::onlyTrashed()
            ->with(['company' => fn ($query) => $query->withTrashed()])
            ->latest('deleted_at')
            ->paginate(10);

        return view('employees.trashed', compact('employees'));
    }

    public function restore(Employee $employee): RedirectResponse
    {
        $employee->restore();

        return redirect()
            ->route('employees.trashed')
            ->with('success', 'Employee restored successfully.');
    }

    public function forceDestroy(Employee $employee): RedirectResponse
    {
        $employee->forceDelete();

        return redirect()
            ->route('employees.trashed')
            ->with('success', 'Employee permanently deleted.');
    }
}
