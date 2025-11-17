<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseItemRequest extends FormRequest
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
            'expense_category_id' => ['sometimes', 'exists:expense_categories,id'],
            'date' => ['sometimes', 'date', 'before_or_equal:today'],
            'merchant_name' => ['sometimes', 'string', 'max:255'],
            'merchant_vat_number' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount' => ['sometimes', 'numeric', 'min:0', 'max:999999.999'],
            'tva_rate' => ['nullable', 'numeric', 'in:0,7,13,19'],
            'currency' => ['nullable', 'string', 'size:3'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'guest_count' => ['nullable', 'integer', 'min:0', 'max:100'],
            'guest_names' => ['nullable', 'array'],
            'guest_names.*' => ['string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'expense_category_id.exists' => 'La catégorie sélectionnée n\'existe pas',
            'date.before_or_equal' => 'La date ne peut pas être dans le futur',
            'amount.min' => 'Le montant doit être positif',
            'tva_rate.in' => 'Le taux de TVA doit être 0%, 7%, 13% ou 19%',
        ];
    }
}
