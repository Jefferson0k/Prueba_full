<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->product->id,
            'codigo' => $this->product->code,
            'nombre' => $this->product->name,
            'descripcion' => $this->product->description,
            'precio_compra' => $this->product->purchase_price,
            'precio_venta' => $this->product->sale_price,
            'unidad' => $this->product->unit_type,
            'fraction_units' => $this->product->fraction_units,
            'es_fraccionable' => $this->product->is_fractionable,
            'stock_actual' => $this->current_stock,
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'sub_sucursal' => optional($this->subBranch)->name,
        ];
    }
}
