<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HospitalScoped;

class Hospital extends Model
{
    use SoftDeletes, HospitalScoped;

    protected $fillable = [
        'name',
        'address',
        'email',
        'phone',
        'status',
        'logo',
        'settings',
        'hospital_type_id',
        'subscription_plan_id',
        'billing_cycle',
        'subscription_start_date',
        'subscription_end_date',
        'trial_end_date',
        'last_billed_at',
        'payment_status',
        'auto_renew',
    ];

    protected $casts = [
        'settings' => 'array',
        'subscription_start_date' => 'datetime',
        'subscription_end_date' => 'datetime',
        'trial_end_date' => 'datetime',
        'last_billed_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hospitalType(): BelongsTo
    {
        return $this->belongsTo(HospitalType::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTrial($query)
    {
        return $query->where('payment_status', 'trial');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('payment_status', 'expired');
    }
}
