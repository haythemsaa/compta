<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'title' => $this->title,
            'description' => $this->description,
            'total_amount' => $this->total_amount,
            'total_ht' => $this->total_ht,
            'total_tva' => $this->total_tva,
            'currency' => $this->currency,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at,
            'approved_at' => $this->approved_at,
            'rejected_at' => $this->rejected_at,
            'rejection_reason' => $this->rejection_reason,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] when $this->relationLoaded('user'),
            'approver' => [
                'id' => $this->approver->id,
                'name' => $this->approver->name,
            ] when $this->relationLoaded('approver') && $this->approver,
            'rejecter' => [
                'id' => $this->rejecter->id,
                'name' => $this->rejecter->name,
            ] when $this->relationLoaded('rejecter') && $this->rejecter,
            'items' => $this->when($this->relationLoaded('items'), function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'date' => $item->date,
                        'category' => $item->category->name ?? null,
                        'merchant_name' => $item->merchant_name,
                        'amount' => $item->amount,
                        'tva_rate' => $item->tva_rate,
                        'description' => $item->description,
                    ];
                });
            }),
            'mileage_expenses' => $this->when($this->relationLoaded('mileageExpenses'), function () {
                return $this->mileageExpenses->map(function ($mileage) {
                    return [
                        'id' => $mileage->id,
                        'date' => $mileage->date,
                        'start_location' => $mileage->start_location,
                        'end_location' => $mileage->end_location,
                        'distance_km' => $mileage->distance_km,
                        'round_trip' => $mileage->round_trip,
                        'total_amount' => $mileage->total_amount,
                    ];
                });
            }),
        ];
    }
}
