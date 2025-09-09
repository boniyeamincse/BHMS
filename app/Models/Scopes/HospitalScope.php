<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class HospitalScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $request = app(\Illuminate\Http\Request::class);

        // Skip scoping for Super Admins (hospital_id = null)
        if (auth()->check() &&
            auth()->user()->hospital_id &&
            !$request->has('is_super_admin')) {
            $builder->where('hospital_id', auth()->user()->hospital_id);
        }
    }
}
