<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'companiesCount' => Company::query()->count(),
            'employeesCount' => Employee::query()->count(),
            'recentCompanies' => Company::query()
                ->withCount('employees')
                ->latest()
                ->take(5)
                ->get(),
            'recentEmployees' => Employee::query()
                ->with('company')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
