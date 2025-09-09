<?php

namespace App\Models;

use App\Traits\HospitalScoped;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodInventory extends Model
{
    use HospitalScoped;

    protected $table = 'blood_inventory';

    protected $fillable = [
        'blood_type',
        'expiry_date',
        'units',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'units' => 'integer',
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', Carbon::now()->addDays($days))
                     ->where('expiry_date', '>', Carbon::now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<=', Carbon::now());
    }

    public function scopeAvailable($query)
    {
        return $query->where('units', '>', 0)
                     ->where('expiry_date', '>', Carbon::now());
    }

    public function getBloodTypeLabelAttribute(): string
    {
        return $this->blood_type;
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    public function getIsExpiredAttribute(): bool
    {
        return Carbon::now()->isAfter($this->expiry_date);
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->days_until_expiry <= 30 && !$this->is_expired;
    }
}