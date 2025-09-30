<?php

namespace App\Http\Resources\Provider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'ruc'          => $this->ruc,
            'razon_social' => $this->razon_social,
            'telefono'     => $this->telefono,
            'direccion'    => $this->direccion,
            'created_at'   => $this->created_at?->format('d-m-Y H:i:s'),
            'updated_at'   => $this->updated_at?->format('d-m-Y H:i:s'),
        ];
    }
}
