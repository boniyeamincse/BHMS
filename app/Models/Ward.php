<?php

namespace App\Models;

use App\Traits\HospitalScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends Model
{
    use HospitalScoped;

    protected $fillable = [
        'name',
        'total_beds',
        'description',
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function getOccupiedBedsAttribute(): int
    {
        return $this->beds()->where('status', 'occupied')->count();
    }

    public function getAvailableBedsAttribute(): int
    {
        return $this->beds()->where('status', 'available')->count();
    }

    public function getTotalBedsInUseAttribute(): int
    {
        return $this->beds()->whereIn('status', ['occupied', 'maintenance'])->count();
    }
}