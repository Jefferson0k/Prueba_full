<?php

namespace App\Pipelines\Kardex;

use Closure;

class FilterByMovementCategory
{
    public function handle($query, Closure $next)
    {
        if (request()->has('movement_category') && request('movement_category')) {
            $query->where('movement_category', request('movement_category'));
        }
        return $next($query);
    }
}
