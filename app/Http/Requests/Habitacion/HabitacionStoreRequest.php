<?php

namespace App\Http\Requests\Habitacion;

use Illuminate\Foundation\Http\FormRequest;

class HabitacionStoreRequest extends FormRequest{
    public function rules(): array{
        return [
            'numero' => ['required', 'string', 'max:10', 'unique:habitaciones,numero'],
            'piso_id' => ['required', 'exists:pisos,id'],
            'tipo_habitacion_id' => ['required', 'exists:tipo_habitaciones,id'],
            'estado' => ['required', 'in:disponible,ocupado,mantenimiento'],
            'descripcion' => ['nullable', 'string'],
        ];
    }
}
