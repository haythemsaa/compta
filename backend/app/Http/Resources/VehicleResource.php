<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'name' => $this->name,
            'brand' => $this->brand,
            'model' => $this->model,
            'registration_number' => $this->registration_number,
            'fiscal_power' => $this->fiscal_power,
            'fuel_type' => $this->fuel_type,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'documents' => $this->documents,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] when $this->relationLoaded('user') && $this->user,
            'mileage_expenses_count' => $this->when(
                $this->relationLoaded('mileageExpenses'),
                fn() => $this->mileageExpenses->count()
            ),
        ];
    }
}
