<?php

namespace App\Pipelines\Room\Filters;

use Closure;

class FilterByFloor
{
    public function handle($query, Closure $next, $filters = [])
    {
        if (isset($filters['floor_id']) && !empty($filters['floor_id'])) {
            $query->where('floor_id', $filters['floor_id']);
        }

        return $next($query);
    }
}