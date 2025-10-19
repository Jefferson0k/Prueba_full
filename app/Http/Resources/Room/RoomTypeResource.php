<?php
namespace App\Http\Resources\Room;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomTypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'capacity' => $this->capacity,
            'base_price_per_hour' => $this->base_price_per_hour,
            'base_price_per_day' => $this->base_price_per_day,
            'base_price_per_night' => $this->base_price_per_night,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}