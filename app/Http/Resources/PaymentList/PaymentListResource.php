<?php

namespace App\Http\Resources\PaymentList;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'payment_code' => $this->payment_code,
            'amount' => (float) $this->amount,
            'payment_method' => $this->payment_method,
            'reference' => $this->reference,
            'payment_date' => $this->payment_date?->toISOString(),
            'status' => $this->status,
            
            // Información del booking relacionado
            'booking' => [
                'id' => $this->booking->id ?? null,
                'booking_code' => $this->booking->booking_code ?? null,
                'customer_name' => $this->booking->customer->full_name ?? null,
                'room_number' => $this->booking->room->room_number ?? null,
            ],
            
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}