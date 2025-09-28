<?php

namespace App\Pipelines\Room\Filters;

use Closure;

class FilterByActive
{
    public function handle($query, Closure $next, $filters = [])
    {
        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', $filters['is_active']);
        } else {
            // Por defecto solo activos
            $query->where('is_active', true);
        }

        return $next($query);
    }
}