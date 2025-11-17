<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expense_report_id',
        'expense_category_id',
        'date',
        'description',
        'merchant_name',
        'merchant_vat_number',
        'amount',
        'amount_ht',
        'tva_rate',
        'tva_amount',
        'currency',
        'exchange_rate',
        'amount_in_default_currency',
        'guest_count',
        'guest_names',
        'ocr_data',
        'ocr_confidence',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:3',
        'amount_ht' => 'decimal:3',
        'tva_rate' => 'decimal:2',
        'tva_amount' => 'decimal:3',
        'exchange_rate' => 'decimal:6',
        'amount_in_default_currency' => 'decimal:3',
        'guest_count' => 'integer',
        'guest_names' => 'array',
        'ocr_data' => 'array',
        'ocr_confidence' => 'decimal:2',
    ];

    // Relationships
    public function expenseReport()
    {
        return $this->belongsTo(ExpenseReport::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    // Helper methods
    public function calculateTVA(float $defaultRate = 19.00): void
    {
        if (!$this->tva_rate) {
            $this->tva_rate = $defaultRate;
        }

        if ($this->amount && !$this->amount_ht) {
            // Calcul à partir du TTC
            $this->amount_ht = $this->amount / (1 + ($this->tva_rate / 100));
            $this->tva_amount = $this->amount - $this->amount_ht;
        } elseif ($this->amount_ht && !$this->amount) {
            // Calcul à partir du HT
            $this->tva_amount = $this->amount_ht * ($this->tva_rate / 100);
            $this->amount = $this->amount_ht + $this->tva_amount;
        }
    }

    protected static function booted()
    {
        static::saved(function ($expenseItem) {
            $expenseItem->expenseReport->calculateTotals();
        });

        static::deleted(function ($expenseItem) {
            $expenseItem->expenseReport->calculateTotals();
        });
    }
}
