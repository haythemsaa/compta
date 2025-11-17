<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'icon',
        'color',
        'description',
        'accounting_code',
        'requires_justification',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'requires_justification' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function expenseItems()
    {
        return $this->hasMany(ExpenseItem::class);
    }

    public function expensePolicies()
    {
        return $this->hasMany(ExpensePolicy::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
