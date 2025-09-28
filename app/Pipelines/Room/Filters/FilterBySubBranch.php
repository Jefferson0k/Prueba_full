<?php

namespace App\Pipelines\Room\Filters;

use Closure;

class FilterBySubBranch
{
    public function handle($query, Closure $next, $filters = [])
    {
        if (isset($filters['sub_branch_id']) && !empty($filters['sub_branch_id'])) {
            $query->whereHas('floor', function ($q) use ($filters) {
                $q->where('sub_branch_id', $filters['sub_branch_id']);
            });
        }

        return $next($query);
    }
}