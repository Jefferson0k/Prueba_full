<?php

namespace App\Http\Resources\Piso;

use Illuminate\Http\Resources\Json\JsonResource;

class PisoResource extends JsonResource{
    public function toArray($request){
        return [
            'id' => $this->id,
            'numero' => $this->numero,
            'descripcion' => $this->descripcion,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
