<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'monthly_price',
        'yearly_price',
        'features',
        'limits',
        'trial_days',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
    ];

    // Relationship with hospitals
    public function hospitals(): HasMany
    {
        return $this->hasMany(Hospital::class);
    }

    // Relationship with transactions
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePaid($query)
    {
        return $query->where('type', 'paid');
    }

    public function scopeTrial($query)
    {
        return $query->where('type', 'trial');
    }
}
