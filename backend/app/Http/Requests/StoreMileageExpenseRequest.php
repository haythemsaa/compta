<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMileageExpenseRequest extends FormRequest
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
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'start_location' => ['required', 'string', 'max:255'],
            'end_location' => ['required', 'string', 'max:255'],
            'distance_km' => ['required', 'numeric', 'min:0.1', 'max:10000'],
            'round_trip' => ['boolean'],
            'description' => ['nullable', 'string', 'max:1000'],
            'purpose' => ['nullable', 'string', 'max:500'],
            'route_data' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'Le véhicule est obligatoire',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas',
            'date.required' => 'La date est obligatoire',
            'date.before_or_equal' => 'La date ne peut pas être dans le futur',
            'start_location.required' => 'Le lieu de départ est obligatoire',
            'end_location.required' => 'Le lieu d\'arrivée est obligatoire',
            'distance_km.required' => 'La distance est obligatoire',
            'distance_km.min' => 'La distance doit être supérieure à 0',
        ];
    }
}
