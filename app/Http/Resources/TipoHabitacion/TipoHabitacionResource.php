<?php

namespace App\Http\Resources\TipoHabitacion;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoHabitacionResource extends JsonResource{
    public function toArray($request){
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
