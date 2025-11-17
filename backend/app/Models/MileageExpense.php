<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MileageExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expense_report_id',
        'vehicle_id',
        'date',
        'start_location',
        'end_location',
        'distance_km',
        'round_trip',
        'fiscal_power',
        'rate_per_km',
        'total_amount',
        'currency',
        'description',
        'purpose',
        'route_data',
    ];

    protected $casts = [
        'date' => 'date',
        'distance_km' => 'decimal:2',
        'round_trip' => 'boolean',
        'fiscal_power' => 'integer',
        'rate_per_km' => 'decimal:3',
        'total_amount' => 'decimal:3',
        'route_data' => 'array',
    ];

    // Relationships
    public function expenseReport()
    {
        return $this->belongsTo(ExpenseReport::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Helper methods
    public function calculateAmount(): void
    {
        $distance = $this->distance_km;

        if ($this->round_trip) {
            $distance *= 2;
        }

        $this->total_amount = $distance * $this->rate_per_km;
    }

    protected static function booted()
    {
        static::creating(function ($mileageExpense) {
            // Stocker la puissance fiscale au moment de la création
            if (!$mileageExpense->fiscal_power && $mileageExpense->vehicle) {
                $mileageExpense->fiscal_power = $mileageExpense->vehicle->fiscal_power;
            }

            // Calculer le barème si non fourni
            if (!$mileageExpense->rate_per_km && $mileageExpense->vehicle) {
                $mileageExpense->rate_per_km = $mileageExpense->vehicle->getMileageRate();
            }

            // Calculer le montant total
            if (!$mileageExpense->total_amount) {
                $mileageExpense->calculateAmount();
            }
        });

        static::saved(function ($mileageExpense) {
            $mileageExpense->expenseReport->calculateTotals();
        });

        static::deleted(function ($mileageExpense) {
            $mileageExpense->expenseReport->calculateTotals();
        });
    }
}
