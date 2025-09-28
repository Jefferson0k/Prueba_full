<?php

namespace App\Pipelines\Room\Filters;

use Closure;

class FilterByAvailability
{
    public function handle($query, Closure $next, $filters = [])
    {
        if (isset($filters['available_from']) && isset($filters['available_to'])) {
            $from = $filters['available_from'];
            $to = $filters['available_to'];
            
            $query->where('status', 'available')
                  ->whereDoesntHave('bookings', function ($q) use ($from, $to) {
                      $q->where('status', 'confirmed')
                        ->where(function ($dateQuery) use ($from, $to) {
                            $dateQuery->whereBetween('check_in', [$from, $to])
                                     ->orWhereBetween('check_out', [$from, $to])
                                     ->orWhere(function ($overlapQuery) use ($from, $to) {
                                         $overlapQuery->where('check_in', '<=', $from)
                                                     ->where('check_out', '>=', $to);
                                     });
                        });
                  });
        }

        return $next($query);
    }
}