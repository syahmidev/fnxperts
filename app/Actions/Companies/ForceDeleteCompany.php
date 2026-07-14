<?php

namespace App\Actions\Companies;

use App\Models\Company;

final class ForceDeleteCompany
{
    public function handle(Company $company): void
    {
        // The foreign-key cascade permanently removes the employees, and the
        // Company model's forceDeleted hook purges the stored logo file.
        $company->forceDelete();
    }
}
