<?php

namespace App\Services;

use App\Models\Room;
use App\Pipelines\Room\RoomPipeline;

class RoomService
{
    protected $roomPipeline;

    public function __construct(RoomPipeline $roomPipeline)
    {
        $this->roomPipeline = $roomPipeline;
    }

    public function search(array $filters = [])
    {
        $query = Room::with([
            'floor.subBranch',
            'roomType',
            'currentBooking'
        ]);

        return $this->roomPipeline->handle($query, $filters);
    }

    public function getAvailableRooms(array $filters = [])
    {
        $filters['status'] = 'available';
        return $this->search($filters);
    }

    public function getRoomsByFloor($floorId, array $filters = [])
    {
        $filters['floor_id'] = $floorId;
        return $this->search($filters);
    }

    public function searchWithStats(array $filters = [])
    {
        $query = $this->search($filters);
        
        $stats = [
            'total' => $query->count(),
            'available' => $query->clone()->where('status', 'available')->count(),
            'occupied' => $query->clone()->where('status', 'occupied')->count(),
            'maintenance' => $query->clone()->where('status', 'maintenance')->count(),
            'cleaning' => $query->clone()->where('status', 'cleaning')->count(),
        ];

        $stats['occupancy_rate'] = $stats['total'] > 0 
            ? round(($stats['occupied'] / $stats['total']) * 100, 2) 
            : 0;

        return [
            'query' => $query,
            'stats' => $stats
        ];
    }
}