<?php

namespace App\Models;

use App\Traits\HospitalScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HospitalScoped;

    protected $fillable = [
        'message',
        'scheduled_date',
        'active',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'active' => 'boolean',
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function scopeActive($query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeInactive($query): Builder
    {
        return $query->where('active', false);
    }

    public function scopeScheduledForToday($query): Builder
    {
        return $query->where('scheduled_date', '=', today());
    }

    public function scopeUpcoming($query, $days = 7): Builder
    {
        return $query->whereBetween('scheduled_date', [today(), today()->addDays($days)])
                     ->where('active', true);
    }

    public function scopePast($query): Builder
    {
        return $query->where('scheduled_date', '<', today())
                     ->orWhere(function ($q) {
                         $q->where('scheduled_date', '=', today())
                           ->where('active', false);
                     });
    }
}