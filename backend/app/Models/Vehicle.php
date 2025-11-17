<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'user_id',
        'name',
        'brand',
        'model',
        'registration_number',
        'fiscal_power',
        'fuel_type',
        'type',
        'documents',
        'is_active',
    ];

    protected $casts = [
        'fiscal_power' => 'integer',
        'documents' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mileageExpenses()
    {
        return $this->hasMany(MileageExpense::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function getMileageRate(int $annualKm = 0): float
    {
        // Barèmes kilométriques tunisiens 2025
        $rates = [
            4 => [0.280, 0.260, 0.240],
            5 => [0.310, 0.290, 0.270],
            6 => [0.340, 0.320, 0.300],
            7 => [0.370, 0.350, 0.330],
            8 => [0.400, 0.380, 0.360],
        ];

        $cv = $this->fiscal_power >= 8 ? 8 : $this->fiscal_power;

        if (!isset($rates[$cv])) {
            $cv = 5; // Default
        }

        if ($annualKm <= 5000) {
            return $rates[$cv][0];
        } elseif ($annualKm <= 10000) {
            return $rates[$cv][1];
        } else {
            return $rates[$cv][2];
        }
    }
}
