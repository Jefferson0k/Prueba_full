<?php

namespace App\Http\Resources\UsoHabitacion;

use Illuminate\Http\Resources\Json\JsonResource;

class UsoHabitacionResource extends JsonResource{
    public function toArray($request){
        return [
            'id' => $this->id,
            'fecha_uso' => $this->fecha_uso,
            'observaciones' => $this->observaciones,
            'cliente' => [
                'id' => $this->cliente?->id,
                'nombre_completo' => $this->cliente?->nombres . ' ' . $this->cliente?->apellidos,
            ],
            'habitacion' => [
                'id' => $this->habitacion?->id,
                'numero' => $this->habitacion?->numero,
            ],
            'horario' => [
                'id' => $this->horario?->id,
                'nombre' => $this->horario?->nombre,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
