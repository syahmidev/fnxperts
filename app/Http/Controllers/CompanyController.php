<?php

namespace App\Http\Controllers;

use App\Actions\Companies\CreateCompany;
use App\Actions\Companies\DeleteCompany;
use App\Actions\Companies\ForceDeleteCompany;
use App\Actions\Companies\RestoreCompany;
use App\Actions\Companies\UpdateCompany;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CompanyController extends Controller
{
    public function index(): View
    {
        $companies = Company::query()
            ->withCount('employees')
            ->latest()
            ->paginate(10);

        return view('companies.index', compact('companies'));
    }

    public function create(): View
    {
        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request, CreateCompany $action): RedirectResponse
    {
        $attributes = $request->validated();
        $logo = $request->file('logo');
        unset($attributes['logo']);

        $company = $action->handle($attributes, $logo);

        return redirect()
            ->route('companies.show', $company)
            ->with('success', 'Company created successfully.');
    }

    public function show(Company $company): View
    {
        $company->load('employees')->loadCount('employees');

        return view('companies.show', compact('company'));
    }

    public function edit(Company $company): View
    {
        return view('companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company, UpdateCompany $action): RedirectResponse
    {
        $attributes = $request->validated();
        $logo = $request->file('logo');
        unset($attributes['logo']);

        $company = $action->handle($company, $attributes, $logo);

        return redirect()
            ->route('companies.show', $company)
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company, DeleteCompany $action): RedirectResponse
    {
        $action->handle($company);

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }

    public function trashed(): View
    {
        $companies = Company::onlyTrashed()
            ->withCount(['employees' => fn ($query) => $query->withTrashed()])
            ->latest('deleted_at')
            ->paginate(10);

        return view('companies.trashed', compact('companies'));
    }

    public function restore(Company $company, RestoreCompany $action): RedirectResponse
    {
        $action->handle($company);

        return redirect()
            ->route('companies.trashed')
            ->with('success', 'Company restored successfully.');
    }

    public function forceDestroy(Company $company, ForceDeleteCompany $action): RedirectResponse
    {
        $action->handle($company);

        return redirect()
            ->route('companies.trashed')
            ->with('success', 'Company permanently deleted.');
    }
}
