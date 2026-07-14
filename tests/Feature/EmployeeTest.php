<?php

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('an administrator can view the employee list', function () {
    Employee::factory()->count(3)->create();

    $this->get(route('employees.index'))
        ->assertOk()
        ->assertViewHas('employees');
});

test('an administrator can view the create form', function () {
    Company::factory()->create();

    $this->get(route('employees.create'))->assertOk();
});

test('an administrator can create an employee', function () {
    $company = Company::factory()->create();

    $this->post(route('employees.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'company_id' => $company->id,
        'email' => 'john@example.com',
        'phone' => '+60123456789',
    ])->assertRedirect();

    $this->assertDatabaseHas('employees', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'company_id' => $company->id,
    ]);
});

test('first name is required', function () {
    $company = Company::factory()->create();

    $this->post(route('employees.store'), [
        'last_name' => 'Doe',
        'company_id' => $company->id,
    ])->assertSessionHasErrors('first_name');
});

test('last name is required', function () {
    $company = Company::factory()->create();

    $this->post(route('employees.store'), [
        'first_name' => 'John',
        'company_id' => $company->id,
    ])->assertSessionHasErrors('last_name');
});

test('company id is required', function () {
    $this->post(route('employees.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
    ])->assertSessionHasErrors('company_id');
});

test('an invalid company id is rejected', function () {
    $this->post(route('employees.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'company_id' => 999999,
    ])->assertSessionHasErrors('company_id');
});

test('an invalid email is rejected', function () {
    $company = Company::factory()->create();

    $this->post(route('employees.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'company_id' => $company->id,
        'email' => 'not-an-email',
    ])->assertSessionHasErrors('email');
});

test('an administrator can view an employee', function () {
    $employee = Employee::factory()->create();

    $this->get(route('employees.show', $employee))
        ->assertOk()
        ->assertSee($employee->first_name);
});

test('an administrator can update an employee', function () {
    $employee = Employee::factory()->create(['first_name' => 'Old']);

    $this->put(route('employees.update', $employee), [
        'first_name' => 'New',
        'last_name' => $employee->last_name,
        'company_id' => $employee->company_id,
    ])->assertRedirect();

    expect($employee->fresh()->first_name)->toBe('New');
});

test('an administrator can soft delete an employee', function () {
    $employee = Employee::factory()->create();

    $this->delete(route('employees.destroy', $employee))->assertRedirect(route('employees.index'));

    $this->assertSoftDeleted($employee);
});

test('the employee listing shows 10 records per page', function () {
    Employee::factory()->count(15)->create();

    $response = $this->get(route('employees.index'))->assertOk();

    expect($response->viewData('employees')->count())->toBe(10);
});

test('the employee listing can be filtered by name', function () {
    $match = Employee::factory()->create(['first_name' => 'Alexander', 'last_name' => 'Ng']);
    $other = Employee::factory()->create(['first_name' => 'Beatrice', 'last_name' => 'Lim']);

    $response = $this->get(route('employees.index', ['search' => 'alex']))->assertOk();

    $ids = $response->viewData('employees')->pluck('id');
    expect($ids)->toContain($match->id)->not->toContain($other->id);
});

test('the employee listing can be filtered by company name', function () {
    $acme = Company::factory()->create(['name' => 'Acme Robotics']);
    $globex = Company::factory()->create(['name' => 'Globex Industries']);
    $match = Employee::factory()->for($acme)->create();
    $other = Employee::factory()->for($globex)->create();

    $response = $this->get(route('employees.index', ['search' => 'robotics']))->assertOk();

    $ids = $response->viewData('employees')->pluck('id');
    expect($ids)->toContain($match->id)->not->toContain($other->id);
});

test('the employee listing displays the related company', function () {
    $company = Company::factory()->create(['name' => 'Globex Corporation']);
    Employee::factory()->for($company)->create();

    $this->get(route('employees.index'))
        ->assertOk()
        ->assertSee('Globex Corporation');
});
