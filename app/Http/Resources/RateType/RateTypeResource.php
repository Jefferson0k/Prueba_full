<?php

namespace App\Http\Resources\RateType;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RateTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'duration_hours' => $this->duration_hours,
            'is_active' => $this->is_active,
        ];
    }
}
