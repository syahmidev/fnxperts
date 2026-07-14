<?php

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Purge the stored logo only when the company is permanently deleted, so a
     * soft-deleted (restorable) company keeps its logo file.
     */
    protected static function booted(): void
    {
        static::forceDeleted(function (Company $company): void {
            if ($company->logo !== null) {
                Storage::disk('public')->delete($company->logo);
            }
        });
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'logo',
        'website',
    ];

    /**
     * @return HasMany<Employee, $this>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Public URL for the stored logo, or null when no logo is set.
     */
    public function logoUrl(): ?string
    {
        return $this->logo !== null
            ? Storage::disk('public')->url($this->logo)
            : null;
    }
}
