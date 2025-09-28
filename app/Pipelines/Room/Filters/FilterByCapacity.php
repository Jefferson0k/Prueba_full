<?php

namespace App\Pipelines\Room\Filters;

use Closure;

class FilterByCapacity{
    public function handle($query, Closure $next, $filters = []){
        if (isset($filters['min_capacity'])) {
            $query->whereHas('roomType', function ($q) use ($filters) {
                $q->where('capacity', '>=', $filters['min_capacity']);
            });
        }

        if (isset($filters['max_capacity'])) {
            $query->whereHas('roomType', function ($q) use ($filters) {
                $q->where('capacity', '<=', $filters['max_capacity']);
            });
        }

        return $next($query);
    }
}
