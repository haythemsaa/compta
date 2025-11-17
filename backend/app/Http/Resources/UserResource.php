<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'job_title' => $this->job_title,
            'department' => $this->department,
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at,
            'created_at' => $this->created_at,
            'organization' => [
                'id' => $this->organization->id,
                'name' => $this->organization->name,
                'currency' => $this->organization->currency,
                'plan' => $this->organization->plan,
            ] when $this->relationLoaded('organization'),
        ];
    }
}
