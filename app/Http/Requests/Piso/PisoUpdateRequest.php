<?php

namespace App\Http\Requests\Piso;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PisoUpdateRequest extends FormRequest{
    public function rules(): array{
        return [
            'numero' => [
                'required',
                'integer',
                Rule::unique('pisos', 'numero')->ignore($this->piso)
            ],
            'descripcion' => ['nullable', 'string'],
        ];
    }
}
