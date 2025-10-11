<?php

namespace App\Http\Resources\Kardex;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class KardexResource extends JsonResource{
    public function toArray($request){
        return [
            'id'                  => $this->id,
            'product_id'          => $this->product_id,
            'product_nombre'      => $this->product->name,
            'movement_detail_id'  => $this->movement_detail_id,
            'sale_id'             => $this->sale_id,
            'precio_total'        => $this->precio_total,
            'SAnteriorCaja'       => $this->SAnteriorCaja,
            'SAnteriorFraccion'   => $this->SAnteriorFraccion,
            'cantidadCaja'        => $this->cantidadCaja,
            'cantidadFraccion'    => $this->cantidadFraccion,
            'SParcialCaja'        => $this->SParcialCaja,
            'SParcialFraccion'    => $this->SParcialFraccion,
            'movement_type'       => $this->movement_type,
            'movement_category'   => $this->movement_category,
            'estado'              => $this->estado,
            'created_at'          => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i') : null,
        ];
    }
}
