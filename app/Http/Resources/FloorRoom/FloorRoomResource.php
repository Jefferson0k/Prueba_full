<?php

namespace App\Http\Resources\FloorRoom;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FloorRoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'floor_number' => $this->floor_number,
            'description' => $this->description,
            'total_rooms' => $this->rooms->count(),
            'available_rooms' => $this->availableRooms->count(),
            'rooms' => $this->rooms->map(function ($room) {
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'name' => $room->name,
                    'status' => $room->status,
                    'is_active' => $room->is_active,
                    'room_type' => $room->roomType?->name,
                ];
            }),
        ];
    }
}
