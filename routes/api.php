<?php

use App\Http\Controllers\Api\CompanyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Public, read-only endpoint that returns a single company together with its
| employees and an employee_count attribute. See README for documentation.
|
*/

Route::get('companies/{company}', [CompanyController::class, 'show'])
    ->name('api.companies.show');
