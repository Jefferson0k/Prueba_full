<?php

namespace App\Http\Resources\Cliente;

use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource{
    public function toArray($request){
        return [
            'id' => $this->id,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'dni' => $this->dni,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
