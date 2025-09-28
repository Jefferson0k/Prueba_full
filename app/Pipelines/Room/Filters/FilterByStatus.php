<?php

namespace App\Pipelines\Room\Filters;

use Closure;

class FilterByStatus
{
    public function handle($query, Closure $next, $filters = [])
    {
        if (isset($filters['status']) && !empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $query->whereIn('status', $filters['status']);
            } else {
                $query->where('status', $filters['status']);
            }
        }

        return $next($query);
    }
}
