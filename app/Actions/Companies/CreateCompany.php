<?php

namespace App\Actions\Companies;

use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class CreateCompany
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(array $attributes, ?UploadedFile $logo = null): Company
    {
        $storedLogoPath = null;

        if ($logo !== null) {
            $storedLogoPath = $logo->store('companies', 'public');
            $attributes['logo'] = $storedLogoPath;
        }

        try {
            return DB::transaction(
                fn (): Company => Company::query()->create($attributes),
            );
        } catch (Throwable $exception) {
            if ($storedLogoPath !== null) {
                Storage::disk('public')->delete($storedLogoPath);
            }

            throw $exception;
        }
    }
}
