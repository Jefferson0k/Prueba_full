<?php

namespace App\Http\Resources\Room;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'code'      => $this->code,
            'is_active' => $this->is_active,
        ];
    }
}
