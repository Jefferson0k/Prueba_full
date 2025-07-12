<?php

namespace App\Http\Requests\Piso;

use Illuminate\Foundation\Http\FormRequest;

class PisoStoreRequest extends FormRequest{
    public function rules(): array{
        return [
            'numero' => ['required', 'integer', 'unique:pisos,numero'],
            'descripcion' => ['nullable', 'string'],
        ];
    }
}
