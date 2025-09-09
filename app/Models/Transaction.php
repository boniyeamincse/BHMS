<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'external_id',
        'amount',
        'currency',
        'payment_method',
        'type',
        'status',
        'hospital_id',
        'subscription_plan_id',
        'metadata',
        'description',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'payment_date' => 'datetime',
    ];

    // Relationships
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['cancelled', 'refunded']);
    }
}
