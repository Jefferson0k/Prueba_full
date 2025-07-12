<?php

namespace App\Http\Requests\TipoHabitacion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoHabitacionUpdateRequest extends FormRequest{
    public function rules(): array{
        return [
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('tipo_habitaciones', 'nombre')->ignore($this->tipoHabitacion),
            ],
            'descripcion' => ['nullable', 'string'],
        ];
    }
}
