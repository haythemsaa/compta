<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MileageExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'expense_report_id' => $this->expense_report_id,
            'date' => $this->date,
            'start_location' => $this->start_location,
            'end_location' => $this->end_location,
            'distance_km' => $this->distance_km,
            'round_trip' => $this->round_trip,
            'fiscal_power' => $this->fiscal_power,
            'rate_per_km' => $this->rate_per_km,
            'total_amount' => $this->total_amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'purpose' => $this->purpose,
            'route_data' => $this->route_data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'vehicle' => [
                'id' => $this->vehicle->id,
                'name' => $this->vehicle->name,
                'brand' => $this->vehicle->brand,
                'model' => $this->vehicle->model,
                'registration_number' => $this->vehicle->registration_number,
                'fiscal_power' => $this->vehicle->fiscal_power,
            ] when $this->relationLoaded('vehicle'),
            'expense_report' => [
                'id' => $this->expenseReport->id,
                'reference' => $this->expenseReport->reference,
                'title' => $this->expenseReport->title,
                'status' => $this->expenseReport->status,
            ] when $this->relationLoaded('expenseReport'),
        ];
    }
}
