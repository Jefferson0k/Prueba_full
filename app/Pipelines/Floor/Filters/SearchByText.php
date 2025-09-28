<?php

namespace App\Pipelines\Floor\Filters;

use Closure;

class SearchByText
{
    public function handle($query, Closure $next, $filters = [])
    {
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('floor_number', 'like', "%{$search}%")
                  ->orWhereHas('subBranch', function ($subQuery) use ($search) {
                      $subQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $next($query);
    }
}
