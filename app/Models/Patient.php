<?php

namespace App\Models;

use App\Traits\HospitalScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HospitalScoped;

    protected $fillable = [
        'name',
        'date_of_birth',
        'type',
        'admission_date',
        'discharge_date',
        'status',
        'ward_id',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'discharge_date' => 'date',
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeAdmitted($query)
    {
        return $query->where('type', 'IPD')->where('status', 'active');
    }

    public function scopeDischarged($query)
    {
        return $query->where('type', 'IPD')->where('status', 'discharged');
    }

    public function scopeOpdVisits($query)
    {
        return $query->where('type', 'OPD');
    }
}