<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'user_id',
        'reference',
        'title',
        'description',
        'total_amount',
        'total_ht',
        'total_tva',
        'currency',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'paid_at',
        'accounting_code',
        'exported_to',
        'exported_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:3',
        'total_ht' => 'decimal:3',
        'total_tva' => 'decimal:3',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'paid_at' => 'datetime',
        'exported_at' => 'datetime',
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }

    public function mileageExpenses()
    {
        return $this->hasMany(MileageExpense::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Helper methods
    public function canSubmit(): bool
    {
        return $this->status === 'draft' && $this->items()->count() > 0;
    }

    public function canApprove(): bool
    {
        return $this->status === 'submitted';
    }

    public function canReject(): bool
    {
        return $this->status === 'submitted';
    }

    public function submit(): void
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function approve(User $approver): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approver->id,
        ]);
    }

    public function reject(User $rejecter, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => $rejecter->id,
            'rejection_reason' => $reason,
        ]);
    }

    public function calculateTotals(): void
    {
        $itemsTotal = $this->items()->sum('amount');
        $mileageTotal = $this->mileageExpenses()->sum('total_amount');

        $this->update([
            'total_amount' => $itemsTotal + $mileageTotal,
        ]);
    }

    protected static function booted()
    {
        static::creating(function ($expenseReport) {
            if (!$expenseReport->reference) {
                $expenseReport->reference = static::generateReference();
            }
        });
    }

    protected static function generateReference(): string
    {
        $year = now()->year;
        $lastReport = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastReport ? (int) substr($lastReport->reference, -4) + 1 : 1;

        return sprintf('ER-%d-%04d', $year, $number);
    }
}
