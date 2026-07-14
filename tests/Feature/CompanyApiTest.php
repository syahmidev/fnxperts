<?php

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('the api returns an existing company with its employees', function () {
    $company = Company::factory()->create();
    Employee::factory()->count(2)->for($company)->create();

    $response = $this->getJson(route('api.companies.show', $company));

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'website',
                'logo_url',
                'employee_count',
                'employees' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'full_name',
                        'email',
                        'phone',
                    ],
                ],
            ],
        ]);

    expect($response->json('data.employees'))->toHaveCount(2);
});

test('the api response includes a correct employee_count', function () {
    $company = Company::factory()->create();
    Employee::factory()->count(3)->for($company)->create();

    $response = $this->getJson(route('api.companies.show', $company));

    expect($response->json('data.employee_count'))->toBe(3)
        ->and($response->json('data.employee_count'))->toBe(count($response->json('data.employees')));
});

test('a company without employees returns an empty employee list', function () {
    $company = Company::factory()->create();

    $response = $this->getJson(route('api.companies.show', $company));

    $response->assertOk();
    expect($response->json('data.employee_count'))->toBe(0)
        ->and($response->json('data.employees'))->toBe([]);
});

test('a missing company returns 404', function () {
    $this->getJson('/api/companies/999999')->assertNotFound();
});

test('a soft-deleted company returns 404', function () {
    $company = Company::factory()->create();
    $company->delete();

    $this->getJson(route('api.companies.show', $company))->assertNotFound();
});

test('logo_url is returned when a logo exists', function () {
    Storage::fake('public');

    $path = UploadedFile::fake()->image('logo.png', 120, 120)->store('companies', 'public');
    $company = Company::factory()->create(['logo' => $path]);

    $response = $this->getJson(route('api.companies.show', $company));

    expect($response->json('data.logo_url'))->toBe(Storage::disk('public')->url($path));
});

test('logo_url is null when no logo exists', function () {
    $company = Company::factory()->create(['logo' => null]);

    $response = $this->getJson(route('api.companies.show', $company));

    expect($response->json('data.logo_url'))->toBeNull();
});
