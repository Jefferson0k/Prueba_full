<?php

namespace App\Http\Requests\Floor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFloorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Floor::class);
    }

    public function rules(): array
    {
        return [
            'sub_branch_id' => 'required|uuid|exists:sub_branches,id',
            'name' => 'required|string|max:255',
            'floor_number' => [
                'required',
                'integer',
                'min:0',
                Rule::unique('floors')->where(function ($query) {
                    return $query->where('sub_branch_id', $this->sub_branch_id)
                                 ->whereNull('deleted_at');
                })
            ],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'sub_branch_id.required' => 'La sub sucursal es requerida.',
            'sub_branch_id.exists' => 'La sub sucursal seleccionada no existe.',
            'name.required' => 'El nombre del piso es requerido.',
            'floor_number.required' => 'El número de piso es requerido.',
            'floor_number.unique' => 'Ya existe un piso con este número en la sub sucursal seleccionada.',
            'floor_number.min' => 'El número de piso debe ser mayor o igual a 0.',
        ];
    }
}