<?php

namespace App\Http\Resources\MovementDetail;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementDetailResource extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'id'              => $this->id,
            'movement_id'     => $this->movement_id,
            'product'         => [
                'id'   => $this->product->id ?? null,
                'name' => $this->product->name ?? null,
            ],
            'unit_price'      => (float) $this->unit_price,
            'boxes'           => (int) $this->boxes,
            'units_per_box'   => (int) $this->units_per_box,
            'expiry_date'     => $this->expiry_date
                ? Carbon::parse($this->expiry_date)->format('d-m-Y')
                : null,
            'total_price'     => (float) $this->total_price,
            'estado'          => $this->estado ?? 1,
            'created_at'      => $this->created_at?->format('d-m-Y H:i:s A'),
            'updated_at'      => $this->updated_at?->format('d-m-Y H:i:s A'),
        ];
    }
}
