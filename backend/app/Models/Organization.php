<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'legal_name',
        'matricule_fiscal',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'currency',
        'default_tva_rate',
        'settings',
        'plan',
        'is_active',
        'trial_ends_at',
        'subscribed_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'default_tva_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'subscribed_at' => 'datetime',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function expenseReports()
    {
        return $this->hasMany(ExpenseReport::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function expensePolicies()
    {
        return $this->hasMany(ExpensePolicy::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    // Helper methods
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && now()->lt($this->trial_ends_at);
    }

    public function isSubscribed(): bool
    {
        return $this->subscribed_at !== null;
    }
}
