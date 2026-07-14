<?php

namespace App\Actions\Companies;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

final class RestoreCompany
{
    public function handle(Company $company): Company
    {
        DB::transaction(function () use ($company): void {
            $company->restore();

            // Restore the employees that were soft-deleted together with the company.
            $company->employees()->onlyTrashed()->restore();
        });

        return $company->refresh();
    }
}
