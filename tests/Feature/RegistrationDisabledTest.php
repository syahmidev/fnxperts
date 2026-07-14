<?php

use App\Models\User;

test('the register routes are not defined', function () {
    expect(fn () => route('register'))->toThrow(Exception::class);
});

test('the register page is not accessible', function () {
    $this->get('/register')->assertNotFound();
});

test('the register endpoint is not accessible', function () {
    $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    expect(User::where('email', 'john@example.com')->exists())->toBeFalse();
});

test('guests are redirected from companies to login', function () {
    $this->get(route('companies.index'))->assertRedirect(route('login'));
});

test('guests are redirected from employees to login', function () {
    $this->get(route('employees.index'))->assertRedirect(route('login'));
});
