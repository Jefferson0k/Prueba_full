<?php

namespace App\Pipelines\Room\Filters;

use Closure;

class FilterByRoomType
{
    public function handle($query, Closure $next, $filters = [])
    {
        if (isset($filters['room_type_id']) && !empty($filters['room_type_id'])) {
            if (is_array($filters['room_type_id'])) {
                $query->whereIn('room_type_id', $filters['room_type_id']);
            } else {
                $query->where('room_type_id', $filters['room_type_id']);
            }
        }

        return $next($query);
    }
}