<?php

namespace App\Http\Resources\Horario;

use Illuminate\Http\Resources\Json\JsonResource;

class HorarioResource extends JsonResource{
    public function toArray($request){
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'duracion_horas' => $this->duracion_horas,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
