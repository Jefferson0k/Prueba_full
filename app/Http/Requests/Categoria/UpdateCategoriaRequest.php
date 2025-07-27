<?php

namespace App\Http\Requests\Categoria;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoriaRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function prepareForValidation(): void{
        $this->merge([
            'nombre' => strtolower($this->input('nombre')),
        ]);
    }
    public function rules(): array{
        return [
            'nombre' => 'required|string|max:100',
            'estado' => ['required', 'boolean'],
        ];
    }
    public function messages(): array{
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'estado.max' => 'El nombre no puede tener más de 100 caracteres.',

            'estado.required' => 'El estado es obligatorio.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
