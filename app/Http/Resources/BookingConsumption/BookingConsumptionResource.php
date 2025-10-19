<?php

namespace App\Http\Resources\BookingConsumption;

use App\Http\Resources\Booking\BookingResource;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingConsumptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'quantity' => (float) $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'total_price' => (float) $this->total_price,
            'consumed_at' => $this->consumed_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Relaciones
            'product' => new ProductResource($this->whenLoaded('product')),
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'created_by_user' => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}