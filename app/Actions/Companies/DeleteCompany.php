<?php

namespace App\Actions\Companies;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

final class DeleteCompany
{
    public function handle(Company $company): void
    {
        DB::transaction(function () use ($company): void {
            // The database foreign-key cascade only fires on a hard delete, so
            // the company's employees are soft-deleted explicitly here.
            $company->employees()->delete();

            $company->delete();
        });
    }
}
