<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByDateRange
{
    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('start_date') && !empty(request('start_date'))) {
            $query->whereDate('check_in', '>=', request('start_date'));
        }

        if (request()->has('end_date') && !empty(request('end_date'))) {
            $query->whereDate('check_in', '<=', request('end_date'));
        }

        return $next($query);
    }
}