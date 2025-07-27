<?php

namespace App\Http\Resources\Producto;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'precio_compra' => $this->precio_compra,
            'precio_venta' =>$this->precio_venta,
            'categoria_id' => $this->categoria_id,
            'Categoria_nombre' => $this->categoria->nombre,
            'estado' => $this->estado,
            'creacion' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),
            'actualizacion' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),
      ];
    }
}
