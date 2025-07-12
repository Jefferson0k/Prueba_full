<?php

namespace App\Http\Resources\Habitacion;

use Illuminate\Http\Resources\Json\JsonResource;

class HabitacionResource extends JsonResource{
    public function toArray($request){
        return [
            'id' => $this->id,
            'numero' => $this->numero,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'piso' => [
                'id' => $this->piso?->id,
                'numero' => $this->piso?->numero,
            ],
            'tipo_habitacion' => [
                'id' => $this->tipo?->id,
                'nombre' => $this->tipo?->nombre,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
