<?php

namespace App\Http\Requests\TipoHabitacion;

use Illuminate\Foundation\Http\FormRequest;

class TipoHabitacionStoreRequest extends FormRequest{
    public function rules(): array{
        return [
            'nombre' => ['required', 'string', 'max:100', 'unique:tipo_habitaciones,nombre'],
            'descripcion' => ['nullable', 'string'],
        ];
    }
}
