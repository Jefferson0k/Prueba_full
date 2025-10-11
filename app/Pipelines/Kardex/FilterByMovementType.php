<?php

namespace App\Pipelines\Kardex;

use Closure;

class FilterByMovementType
{
    public function handle($query, Closure $next)
    {
        if (request()->has('movement_type') && request('movement_type')) {
            $query->where('movement_type', request('movement_type'));
        }
        return $next($query);
    }
}
