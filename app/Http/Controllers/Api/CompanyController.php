<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Company;

class CompanyController extends Controller
{
    public function show(Company $company): CompanyResource
    {
        $company->load('employees')->loadCount('employees');

        return new CompanyResource($company);
    }
}
