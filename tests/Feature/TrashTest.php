<?php

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('the trashed companies listing shows soft-deleted companies', function () {
    $trashed = Company::factory()->create();
    $trashed->delete();
    $active = Company::factory()->create();

    $response = $this->get(route('companies.trashed'))->assertOk();

    expect($response->viewData('companies')->pluck('id'))
        ->toContain($trashed->id)
        ->not->toContain($active->id);
});

test('restoring a company also restores its cascaded employees', function () {
    $company = Company::factory()->create();
    $employees = Employee::factory()->count(3)->for($company)->create();

    $this->delete(route('companies.destroy', $company));
    $this->assertSoftDeleted($company);

    $this->put(route('companies.restore', $company))->assertRedirect(route('companies.trashed'));

    expect(Company::whereKey($company->id)->exists())->toBeTrue();
    foreach ($employees as $employee) {
        expect(Employee::whereKey($employee->id)->exists())->toBeTrue();
    }
});

test('force deleting a company permanently removes it, its employees, and its logo', function () {
    Storage::fake('public');

    $company = Company::factory()->create();
    $this->put(route('companies.update', $company), [
        'name' => $company->name,
        'logo' => UploadedFile::fake()->image('logo.png', 120, 120),
    ]);
    $logo = $company->fresh()->logo;
    $employee = Employee::factory()->for($company)->create();

    $this->delete(route('companies.destroy', $company));

    $this->delete(route('companies.force-destroy', $company))->assertRedirect(route('companies.trashed'));

    $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    Storage::disk('public')->assertMissing($logo);
});

test('the trashed employees listing shows soft-deleted employees', function () {
    $trashed = Employee::factory()->create();
    $trashed->delete();
    $active = Employee::factory()->create();

    $response = $this->get(route('employees.trashed'))->assertOk();

    expect($response->viewData('employees')->pluck('id'))
        ->toContain($trashed->id)
        ->not->toContain($active->id);
});

test('an administrator can restore an employee', function () {
    $employee = Employee::factory()->create();
    $employee->delete();

    $this->put(route('employees.restore', $employee))->assertRedirect(route('employees.trashed'));

    expect(Employee::whereKey($employee->id)->exists())->toBeTrue();
});

test('an administrator can permanently delete an employee', function () {
    $employee = Employee::factory()->create();
    $employee->delete();

    $this->delete(route('employees.force-destroy', $employee))->assertRedirect(route('employees.trashed'));

    $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
});

test('guests cannot access trash routes', function () {
    auth()->logout();

    $this->get(route('companies.trashed'))->assertRedirect(route('login'));
    $this->get(route('employees.trashed'))->assertRedirect(route('login'));
});
