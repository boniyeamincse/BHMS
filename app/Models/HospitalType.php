<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HospitalType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'features',
        'status',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    // Relationship with hospitals
    public function hospitals(): HasMany
    {
        return $this->hasMany(Hospital::class);
    }

    // Scope for active hospital types
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
