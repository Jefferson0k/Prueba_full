<?php

namespace App\Http\Requests\UsoHabitacion;

use Illuminate\Foundation\Http\FormRequest;

class UsoHabitacionUpdateRequest extends FormRequest{
    public function rules(): array{
        return [
            'habitacion_id' => ['required', 'exists:habitaciones,id'],
            'cliente_id' => ['required', 'exists:clientes,id'],
            'horario_id' => ['required', 'exists:horarios,id'],
            'fecha_uso' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
        ];
    }
}
