<?php

namespace App\Http\Resources\PaymentMethodTotal;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodTotalResource extends JsonResource
{
    public function toArray($request)
    {
        // Si es array (versión segura)
        if (is_array($this->resource)) {
            return [
                'payment_method_id' => $this->resource['payment_method_id'],
                'payment_method_name' => $this->resource['payment_method_name'],
                'payment_method_code' => $this->resource['payment_method_code'],
                'total_amount' => $this->resource['total_amount'],
                'payment_count' => $this->resource['payment_count'],
                'percentage' => $this->resource['percentage'],
            ];
        }

        // Si es objeto Eloquent
        return [
            'payment_method_id' => $this->payment_method_id,
            'payment_method_name' => $this->payment_method_name,
            'payment_method_code' => $this->payment_method_code,
            'total_amount' => $this->total_amount,
            'payment_count' => $this->payment_count,
            'percentage' => $this->percentage,
        ];
    }
}