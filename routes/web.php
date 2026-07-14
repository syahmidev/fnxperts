<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Trash management. Declared before the resource routes so that
    // "companies/trashed" is not captured by the "companies/{company}" wildcard.
    Route::get('companies/trashed', [CompanyController::class, 'trashed'])->name('companies.trashed');
    Route::put('companies/{company}/restore', [CompanyController::class, 'restore'])
        ->name('companies.restore')->withTrashed();
    Route::delete('companies/{company}/force', [CompanyController::class, 'forceDestroy'])
        ->name('companies.force-destroy')->withTrashed();

    Route::get('employees/trashed', [EmployeeController::class, 'trashed'])->name('employees.trashed');
    Route::put('employees/{employee}/restore', [EmployeeController::class, 'restore'])
        ->name('employees.restore')->withTrashed();
    Route::delete('employees/{employee}/force', [EmployeeController::class, 'forceDestroy'])
        ->name('employees.force-destroy')->withTrashed();

    Route::resource('companies', CompanyController::class);
    Route::resource('employees', EmployeeController::class);
});

require __DIR__.'/settings.php';
