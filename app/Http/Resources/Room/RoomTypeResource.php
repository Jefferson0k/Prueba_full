<?php

namespace App\Http\Resources\Room;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomTypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'capacity'            => $this->capacity,
            'base_price_per_hour' => $this->base_price_per_hour,
            'base_price_per_day'  => $this->base_price_per_day,
            'base_price_per_night'=> $this->base_price_per_night,
        ];
    }
}
