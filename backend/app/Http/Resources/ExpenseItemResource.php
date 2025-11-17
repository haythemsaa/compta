<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'expense_report_id' => $this->expense_report_id,
            'date' => $this->date,
            'merchant_name' => $this->merchant_name,
            'merchant_vat_number' => $this->merchant_vat_number,
            'description' => $this->description,
            'amount' => $this->amount,
            'amount_ht' => $this->amount_ht,
            'tva_rate' => $this->tva_rate,
            'tva_amount' => $this->tva_amount,
            'currency' => $this->currency,
            'exchange_rate' => $this->exchange_rate,
            'amount_in_default_currency' => $this->amount_in_default_currency,
            'guest_count' => $this->guest_count,
            'guest_names' => $this->guest_names,
            'ocr_confidence' => $this->ocr_confidence,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'code' => $this->category->code,
                'icon' => $this->category->icon,
                'color' => $this->category->color,
            ] when $this->relationLoaded('category'),
            'media' => $this->when($this->relationLoaded('media'), function () {
                return $this->media->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'file_name' => $media->file_name,
                        'file_type' => $media->file_type,
                        'file_size' => $media->file_size,
                        'file_path' => $media->file_path,
                        'ocr_status' => $media->ocr_status,
                    ];
                });
            }),
            'expense_report' => [
                'id' => $this->expenseReport->id,
                'reference' => $this->expenseReport->reference,
                'title' => $this->expenseReport->title,
                'status' => $this->expenseReport->status,
            ] when $this->relationLoaded('expenseReport'),
        ];
    }
}
