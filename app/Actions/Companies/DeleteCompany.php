<?php

namespace App\Actions\Companies;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class DeleteCompany
{
    public function handle(Company $company): void
    {
        $logoPath = $company->logo;

        DB::transaction(function () use ($company): void {
            // Related employees are removed by the foreign-key cascade.
            $company->delete();
        });

        if ($logoPath !== null) {
            Storage::disk('public')->delete($logoPath);
        }
    }
}
