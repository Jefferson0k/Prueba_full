<?php

namespace App\Pipelines\Room\Filters;

use Closure;

class FilterByDateRange{
    public function handle($query, Closure $next, $filters = []){
        if (isset($filters['created_from'])) {
            $query->where('created_at', '>=', $filters['created_from']);
        }

        if (isset($filters['created_to'])) {
            $query->where('created_at', '<=', $filters['created_to']);
        }

        if (isset($filters['updated_from'])) {
            $query->where('updated_at', '>=', $filters['updated_from']);
        }

        if (isset($filters['updated_to'])) {
            $query->where('updated_at', '<=', $filters['updated_to']);
        }

        return $next($query);
    }
}