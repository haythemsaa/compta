<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'registration_number' => ['required', 'string', 'max:50', 'unique:vehicles,registration_number'],
            'fiscal_power' => ['required', 'integer', 'min:1', 'max:20'],
            'fuel_type' => ['required', 'in:essence,diesel,gpl,electrique,hybride'],
            'type' => ['required', 'in:personal,company'],
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
            'name.required' => 'Le nom du véhicule est obligatoire',
            'brand.required' => 'La marque est obligatoire',
            'model.required' => 'Le modèle est obligatoire',
            'registration_number.required' => 'Le numéro d\'immatriculation est obligatoire',
            'registration_number.unique' => 'Ce numéro d\'immatriculation existe déjà',
            'fiscal_power.required' => 'La puissance fiscale est obligatoire',
            'fiscal_power.min' => 'La puissance fiscale doit être au minimum de 1 CV',
            'fuel_type.required' => 'Le type de carburant est obligatoire',
            'fuel_type.in' => 'Le type de carburant doit être essence, diesel, GPL, électrique ou hybride',
            'type.required' => 'Le type de véhicule est obligatoire',
            'type.in' => 'Le type doit être personnel ou société',
        ];
    }
}
