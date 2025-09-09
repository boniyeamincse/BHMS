<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\Scopes\HospitalScope;

trait HospitalScoped
{
    protected static function bootHospitalScoped()
    {
        static::addGlobalScope(new HospitalScope);
    }
}