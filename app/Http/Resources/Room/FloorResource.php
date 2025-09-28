<?php

namespace App\Http\Resources\Room;

use Illuminate\Http\Resources\Json\JsonResource;

class FloorResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'floor_number' => $this->floor_number,
            'branch'       => new BranchResource($this->whenLoaded('branch')),
        ];
    }
}
