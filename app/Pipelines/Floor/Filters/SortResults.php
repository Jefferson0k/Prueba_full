<?php

namespace App\Pipelines\Floor\Filters;

use Closure;

class SortResults
{
    public function handle($query, Closure $next, $filters = [])
    {
        $sortBy = $filters['sort_by'] ?? 'floor_number';
        $sortOrder = $filters['sort_order'] ?? 'asc';

        $allowedSorts = ['name', 'floor_number', 'created_at', 'updated_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $next($query);
    }
}