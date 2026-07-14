<?php

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('an administrator can view the company list', function () {
    Company::factory()->count(3)->create();

    $this->get(route('companies.index'))
        ->assertOk()
        ->assertViewHas('companies');
});

test('an administrator can view the create form', function () {
    $this->get(route('companies.create'))->assertOk();
});

test('an administrator can create a company', function () {
    $this->post(route('companies.store'), [
        'name' => 'Acme Inc',
        'email' => 'hello@acme.test',
        'website' => 'https://acme.test',
    ])->assertRedirect();

    $this->assertDatabaseHas('companies', [
        'name' => 'Acme Inc',
        'email' => 'hello@acme.test',
        'website' => 'https://acme.test',
    ]);
});

test('company name is required', function () {
    $this->post(route('companies.store'), ['name' => ''])
        ->assertSessionHasErrors('name');
});

test('invalid email is rejected', function () {
    $this->post(route('companies.store'), [
        'name' => 'Acme',
        'email' => 'not-an-email',
    ])->assertSessionHasErrors('email');
});

test('invalid website is rejected', function () {
    $this->post(route('companies.store'), [
        'name' => 'Acme',
        'website' => 'not a url',
    ])->assertSessionHasErrors('website');
});

test('a non-image logo file is rejected', function () {
    Storage::fake('public');

    $this->post(route('companies.store'), [
        'name' => 'Acme',
        'logo' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ])->assertSessionHasErrors('logo');
});

test('a logo smaller than 100x100 is rejected', function () {
    Storage::fake('public');

    $this->post(route('companies.store'), [
        'name' => 'Acme',
        'logo' => UploadedFile::fake()->image('small.png', 50, 50),
    ])->assertSessionHasErrors('logo');
});

test('a valid logo can be uploaded and is stored', function () {
    Storage::fake('public');

    $this->post(route('companies.store'), [
        'name' => 'Acme',
        'logo' => UploadedFile::fake()->image('logo.png', 120, 120),
    ])->assertRedirect();

    $company = Company::firstWhere('name', 'Acme');

    expect($company->logo)->not->toBeNull();
    Storage::disk('public')->assertExists($company->logo);
});

test('an administrator can view a company', function () {
    $company = Company::factory()->create();

    $this->get(route('companies.show', $company))
        ->assertOk()
        ->assertSee($company->name);
});

test('an administrator can update a company', function () {
    $company = Company::factory()->create(['name' => 'Old Name']);

    $this->put(route('companies.update', $company), [
        'name' => 'New Name',
    ])->assertRedirect();

    expect($company->fresh()->name)->toBe('New Name');
});

test('replacing a logo removes the old logo', function () {
    Storage::fake('public');

    $company = Company::factory()->create();
    $this->put(route('companies.update', $company), [
        'name' => $company->name,
        'logo' => UploadedFile::fake()->image('first.png', 120, 120),
    ]);

    $oldLogo = $company->fresh()->logo;
    Storage::disk('public')->assertExists($oldLogo);

    $this->put(route('companies.update', $company), [
        'name' => $company->name,
        'logo' => UploadedFile::fake()->image('second.png', 120, 120),
    ]);

    $newLogo = $company->fresh()->logo;

    expect($newLogo)->not->toBe($oldLogo);
    Storage::disk('public')->assertMissing($oldLogo);
    Storage::disk('public')->assertExists($newLogo);
});

test('an administrator can soft delete a company', function () {
    $company = Company::factory()->create();

    $this->delete(route('companies.destroy', $company))->assertRedirect(route('companies.index'));

    $this->assertSoftDeleted($company);
});

test('soft deleting keeps the logo but force deleting removes it', function () {
    Storage::fake('public');

    $company = Company::factory()->create();
    $this->put(route('companies.update', $company), [
        'name' => $company->name,
        'logo' => UploadedFile::fake()->image('logo.png', 120, 120),
    ]);
    $logo = $company->fresh()->logo;
    Storage::disk('public')->assertExists($logo);

    // Soft delete retains the file so the record remains restorable.
    $this->delete(route('companies.destroy', $company));
    Storage::disk('public')->assertExists($logo);

    // Permanent deletion purges the file.
    Company::withTrashed()->findOrFail($company->id)->forceDelete();
    Storage::disk('public')->assertMissing($logo);
});

test('deleting a company cascades a soft delete to its employees', function () {
    $company = Company::factory()->create();
    $employees = Employee::factory()->count(3)->for($company)->create();

    $this->delete(route('companies.destroy', $company));

    foreach ($employees as $employee) {
        $this->assertSoftDeleted($employee);
    }
});

test('a soft-deleted company is hidden from the listing', function () {
    $company = Company::factory()->create();
    $company->delete();

    $response = $this->get(route('companies.index'))->assertOk();

    expect($response->viewData('companies')->contains($company))->toBeFalse();
});

test('the company listing shows 10 records per page', function () {
    Company::factory()->count(15)->create();

    $response = $this->get(route('companies.index'))->assertOk();

    expect($response->viewData('companies')->count())->toBe(10);
});

test('the company listing can be filtered by a search term', function () {
    $match = Company::factory()->create(['name' => 'Acme Robotics']);
    $other = Company::factory()->create(['name' => 'Globex Industries']);

    $response = $this->get(route('companies.index', ['search' => 'robot']))->assertOk();

    $ids = $response->viewData('companies')->pluck('id');
    expect($ids)->toContain($match->id)->not->toContain($other->id);
});

test('search matches company email', function () {
    $match = Company::factory()->create(['email' => 'hello@needle.test']);
    Company::factory()->create(['email' => 'nope@haystack.test']);

    $response = $this->get(route('companies.index', ['search' => 'NEEDLE']))->assertOk();

    expect($response->viewData('companies')->pluck('id'))->toContain($match->id)->toHaveCount(1);
});

test('the search term is preserved across pagination links', function () {
    Company::factory()->count(15)->create(['name' => 'Searchable Co']);

    $response = $this->get(route('companies.index', ['search' => 'Searchable']))->assertOk();

    expect($response->viewData('companies')->count())->toBe(10);
    $response->assertSee('search=Searchable', false);
});

test('the company listing includes employee counts', function () {
    $company = Company::factory()->create();
    Employee::factory()->count(4)->for($company)->create();

    $response = $this->get(route('companies.index'))->assertOk();

    $listed = $response->viewData('companies')->firstWhere('id', $company->id);

    expect($listed->employees_count)->toBe(4);
});
