<?php

namespace App\Pipelines\Floor\Filters;

use Closure;

class WithRoomCounts
{
    public function handle($query, Closure $next, $filters = [])
    {
        if (isset($filters['with_room_counts']) && $filters['with_room_counts']) {
            $query->withCount(['rooms', 'availableRooms']);
        }

        return $next($query);
    }
}