<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpensePolicy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'expense_category_id',
        'name',
        'description',
        'max_amount',
        'max_amount_per_day',
        'max_amount_per_month',
        'requires_manager_approval',
        'requires_accountant_approval',
        'requires_daf_approval',
        'auto_approve_below',
        'rules',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'max_amount' => 'decimal:3',
        'max_amount_per_day' => 'decimal:3',
        'max_amount_per_month' => 'decimal:3',
        'requires_manager_approval' => 'boolean',
        'requires_accountant_approval' => 'boolean',
        'requires_daf_approval' => 'boolean',
        'auto_approve_below' => 'decimal:3',
        'rules' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCategory($query, $categoryId)
    {
        return $query->where(function ($q) use ($categoryId) {
            $q->where('expense_category_id', $categoryId)
              ->orWhereNull('expense_category_id');
        });
    }

    // Helper methods
    public function canAutoApprove(float $amount): bool
    {
        return $this->auto_approve_below && $amount <= $this->auto_approve_below;
    }

    public function exceedsLimit(float $amount, string $period = 'single'): bool
    {
        switch ($period) {
            case 'day':
                return $this->max_amount_per_day && $amount > $this->max_amount_per_day;
            case 'month':
                return $this->max_amount_per_month && $amount > $this->max_amount_per_month;
            default:
                return $this->max_amount && $amount > $this->max_amount;
        }
    }
}
