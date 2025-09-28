<?php

namespace App\Http\Requests\SubBranch;

use App\Models\SubBranch;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubBranchRequest extends FormRequest{
    public function authorize(): bool{
        return $this->user()->can('create', SubBranch::class);
    }
    public function rules(): array{
        return [
            'branch_id'   => ['required', 'exists:branches,id'],
            'name'        => ['required', 'string', 'max:255'],
            'address'     => ['required', 'string', 'max:500'],
            'phone'       => ['required', 'string', 'max:20'],
            'is_active'   => ['required', 'boolean'],
        ];
    }
    public function messages(): array{
        return [
            'branch_id.required'   => 'La sucursal principal es obligatoria.',
            'branch_id.exists'     => 'La sucursal principal seleccionada no existe.',

            'name.required'        => 'El nombre es obligatorio.',
            'name.string'          => 'El nombre debe ser texto.',
            'name.max'             => 'El nombre no puede superar los 255 caracteres.',

            'address.required'     => 'La dirección es obligatoria.',
            'address.string'       => 'La dirección debe ser texto.',
            'address.max'          => 'La dirección no puede superar los 500 caracteres.',

            'phone.required'       => 'El teléfono es obligatorio.',
            'phone.string'         => 'El teléfono debe ser texto.',
            'phone.max'            => 'El teléfono no puede superar los 20 caracteres.',

            'is_active.required'   => 'El estado activo es obligatorio.',
            'is_active.boolean'    => 'El estado debe ser verdadero o falso.',
        ];
    }
}
