<?php

namespace App\Http\Requests\SubBranch;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        $subBranch = $this->route('sub_branch');
        return $this->user()->can('update', $subBranch);
    }

    public function rules(): array
    {
        return [
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'code' => 'sometimes|string|max:50|unique:sub_branches,code,' . $this->route('sub_branch'),
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'branch_id.required' => 'La sucursal principal es obligatoria.',
            'branch_id.exists' => 'La sucursal principal seleccionada no existe.',
            'name.required' => 'El nombre del local es obligatorio.',
            'code.unique' => 'El código ya está en uso.',
        ];
    }
}