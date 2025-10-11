<?php

namespace App\Pipelines\Inventory;

use Closure;

class FilterByActive
{
    public function handle($query, Closure $next)
    {
        if (request()->boolean('only_active')) {
            $query->where('is_active', true);
        }
        return $next($query);
    }
}
