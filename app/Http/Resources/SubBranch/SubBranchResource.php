<?php

namespace App\Http\Resources\SubBranch;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SubBranchResource extends JsonResource{
    public function toArray($request): array{
        return [
            'id'         => $this->id,
            'branch_id'  => $this->branch_id,
            'name'       => $this->name,
            'code'       => $this->code,
            'address'    => $this->address,
            'phone'      => $this->phone,
            'is_active'  => $this->is_active,
            'floors_count' => $this->when(isset($this->floors_count), $this->floors_count),
            'rooms_count' => $this->when(isset($this->rooms_count), $this->rooms_count),
            'available_rooms_count' => $this->when(isset($this->available_rooms_count), $this->available_rooms_count),
            'pisos' => $this->when(isset($this->floors_count), $this->floors_count),
            'habitaciones' => $this->when(isset($this->rooms_count), $this->rooms_count),
            'creacion'   => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),
            'actualizacion' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),
        ];
    }
}