<?php

namespace App\Http\Resources\Floor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FloorResource extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'sub_branch_id' => $this->sub_branch_id,
            'name' => $this->name,
            'floor_number' => $this->floor_number,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'sub_branch' => $this->whenLoaded('subBranch', function () {
                return [
                    'id' => $this->subBranch->id,
                    'name' => $this->subBranch->name,
                    'code' => $this->subBranch->code,
                ];
            }),
            'rooms' => $this->whenLoaded(relationship: 'rooms'),
            'rooms_count' => $this->when(isset($this->rooms_count), $this->rooms_count),
            'available_rooms_count' => $this->when(isset($this->available_rooms_count), $this->available_rooms_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}