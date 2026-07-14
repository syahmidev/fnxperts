<?php

namespace App\Http\Resources;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Company
 */
class CompanyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'website' => $this->website,
            'logo_url' => $this->logoUrl(),
            'employee_count' => $this->whenCounted('employees'),
            'employees' => EmployeeResource::collection(
                $this->whenLoaded('employees'),
            ),
        ];
    }
}
