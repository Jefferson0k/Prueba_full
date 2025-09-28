<?php

namespace App\Pipelines\Room\Filters;

use Closure;

class SortResults
{
    public function handle($query, Closure $next, $filters = [])
    {
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $allowedSorts = [
            'room_number', 'name', 'status', 'created_at', 'updated_at',
            'floor_name', 'room_type_name'
        ];

        if (in_array($sortBy, $allowedSorts)) {
            switch ($sortBy) {
                case 'floor_name':
                    $query->join('floors', 'rooms.floor_id', '=', 'floors.id')
                          ->orderBy('floors.name', $sortOrder)
                          ->select('rooms.*');
                    break;
                    
                case 'room_type_name':
                    $query->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                          ->orderBy('room_types.name', $sortOrder)
                          ->select('rooms.*');
                    break;
                    
                default:
                    $query->orderBy($sortBy, $sortOrder);
                    break;
            }
        }

        return $next($query);
    }
}
