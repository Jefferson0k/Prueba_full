<?php

namespace App\Http\Requests\Horario;

use Illuminate\Foundation\Http\FormRequest;

class HorarioStoreRequest extends FormRequest{
    public function rules(): array{
        return [
            'nombre' => ['required', 'string', 'max:100', 'unique:horarios,nombre'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'duracion_horas' => ['required', 'integer', 'min:1'],
        ];
    }
}
