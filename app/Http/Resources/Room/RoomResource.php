<?php

namespace App\Http\Resources\Room;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'room_number' => $this->room_number,
            'name'        => $this->name,
            'description' => $this->description,
            'status'      => $this->status,
            'is_active'   => $this->is_active,
            'full_name'   => $this->full_name,
            'floor'       => $this->floor ? new FloorResource($this->floor) : null,
            'room_type'   => $this->roomType ? new RoomTypeResource($this->roomType) : null,
            'current_booking' => $this->currentBooking ? [
                'guest_name' => optional($this->currentBooking->guest)->name,
                'check_in'   => $this->currentBooking->check_in?->toDateTimeString(),
                'check_out'  => $this->currentBooking->check_out?->toDateTimeString(),
            ] : null,
            'created_at'  => $this->created_at?->toDateTimeString(),
            'updated_at'  => $this->updated_at?->toDateTimeString(),
        ];
    }
}
