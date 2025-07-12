<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;

class ClienteStoreRequest extends FormRequest{
    public function rules(): array{
        return [
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'dni' => ['required', 'string', 'size:8', 'unique:clientes,dni'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string'],
        ];
    }
}
