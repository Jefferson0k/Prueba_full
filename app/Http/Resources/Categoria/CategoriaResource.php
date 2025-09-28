<?php

namespace App\Http\Resources\Categoria;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    public function toArray(Request $request): array{
        Carbon::setLocale('es');
        return [
            'id'            => $this->id,
            'nombre'        => $this->name,
            'codigo'        => $this->code,
            'descripcion'   => $this->description,
            'estado'        => $this->is_active,
            'creacion'      => Carbon::parse($this->created_at)
                                    ->translatedFormat('l d \d\e F \d\e Y h:i:s a'),
            'actualizacion' => Carbon::parse($this->updated_at)
                                    ->translatedFormat('l d \d\e F \d\e Y h:i:s a'),
        ];
    }
}
