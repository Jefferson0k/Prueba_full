<?php

namespace App\Http\Resources\Room;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomStatusLogResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'previous_status' => $this->previous_status,
            'new_status'      => $this->new_status,
            'reason'          => $this->reason,
            'changed_at'      => $this->changed_at?->toDateTimeString(),
            'changed_by'      => $this->whenLoaded('changedBy', fn() => [
                'id'   => $this->changedBy->id,
                'name' => $this->changedBy->name,
            ]),
        ];
    }
}
