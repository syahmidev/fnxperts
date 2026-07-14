<?php

namespace App\Actions\Companies;

use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class UpdateCompany
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Company $company, array $attributes, ?UploadedFile $logo = null): Company
    {
        $previousLogoPath = $company->logo;
        $storedLogoPath = null;

        if ($logo !== null) {
            $storedLogoPath = $logo->store('companies', 'public');
            $attributes['logo'] = $storedLogoPath;
        }

        try {
            DB::transaction(function () use ($company, $attributes): void {
                $company->update($attributes);
            });
        } catch (Throwable $exception) {
            if ($storedLogoPath !== null) {
                Storage::disk('public')->delete($storedLogoPath);
            }

            throw $exception;
        }

        // Only remove the old logo once the update has succeeded and a
        // replacement was actually uploaded.
        if ($storedLogoPath !== null && $previousLogoPath !== null) {
            Storage::disk('public')->delete($previousLogoPath);
        }

        return $company->refresh();
    }
}
