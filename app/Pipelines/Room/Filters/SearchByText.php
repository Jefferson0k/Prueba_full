<?php

namespace App\Pipelines\Room\Filters;

use Closure;

class SearchByText
{
    public function handle($query, Closure $next, $filters = [])
    {
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            
            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('floor', function ($floorQuery) use ($search) {
                      $floorQuery->where('name', 'like', "%{$search}%")
                                 ->orWhereHas('subBranch', function ($subBranchQuery) use ($search) {
                                     $subBranchQuery->where('name', 'like', "%{$search}%");
                                 });
                  })
                  ->orWhereHas('roomType', function ($typeQuery) use ($search) {
                      $typeQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $next($query);
    }
}
