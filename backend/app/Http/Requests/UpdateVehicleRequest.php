<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'exists:users,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'brand' => ['sometimes', 'string', 'max:100'],
            'model' => ['sometimes', 'string', 'max:100'],
            'registration_number' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('vehicles')->ignore($this->route('vehicle')),
            ],
            'fiscal_power' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'fuel_type' => ['sometimes', 'in:essence,diesel,gpl,electrique,hybride'],
            'type' => ['sometimes', 'in:personal,company'],
            'documents' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'registration_number.unique' => 'Ce numéro d\'immatriculation existe déjà',
            'fiscal_power.min' => 'La puissance fiscale doit être au minimum de 1 CV',
            'fuel_type.in' => 'Le type de carburant doit être essence, diesel, GPL, électrique ou hybride',
            'type.in' => 'Le type doit être personnel ou société',
        ];
    }
}
