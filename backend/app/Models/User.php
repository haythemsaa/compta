<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'password',
        'phone',
        'role',
        'job_title',
        'department',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function expenseReports()
    {
        return $this->hasMany(ExpenseReport::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function approvedReports()
    {
        return $this->hasMany(ExpenseReport::class, 'approved_by');
    }

    public function rejectedReports()
    {
        return $this->hasMany(ExpenseReport::class, 'rejected_by');
    }

    public function uploadedMedia()
    {
        return $this->hasMany(Media::class, 'uploaded_by');
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['manager', 'daf', 'admin']);
    }

    public function isAccountant(): bool
    {
        return in_array($this->role, ['accountant', 'daf', 'admin']);
    }

    public function canApprove(): bool
    {
        return $this->isManager();
    }
}
