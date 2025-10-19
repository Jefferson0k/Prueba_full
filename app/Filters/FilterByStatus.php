<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByStatus
{
    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('status') && !empty(request('status'))) {
            $status = request('status');
            
            if (is_array($status)) {
                $query->whereIn('status', $status);
            } else {
                $query->where('status', $status);
            }
        }

        return $next($query);
    }
}