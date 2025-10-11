<?php

namespace App\Http\Requests\Floor;

use Illuminate\Foundation\Http\FormRequest;

class CreateMultipleFloorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Floor::class);
    }

    public function rules(): array
    {
        return [
            'sub_branch_id' => 'required|uuid|exists:sub_branches,id',
            'nombre_base' => 'nullable|string|max:255',
            'cantidad' => 'required|integer|min:1|max:50',
            'inicio' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'sub_branch_id.required' => 'La sub sucursal es requerida.',
            'sub_branch_id.exists' => 'La sub sucursal seleccionada no existe.',
            'cantidad.required' => 'La cantidad de pisos es requerida.',
            'cantidad.min' => 'Debes crear al menos un piso.',
            'cantidad.max' => 'No puedes crear más de 50 pisos de una sola vez.',
        ];
    }
}
