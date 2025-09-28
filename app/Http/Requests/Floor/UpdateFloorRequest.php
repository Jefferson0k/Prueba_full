<?php

namespace App\Http\Requests\Floor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFloorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('floor'));
    }

    public function rules(): array
    {
        $floor = $this->route('floor');
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'floor_number' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
                Rule::unique('floors')->where(function ($query) use ($floor) {
                    return $query->where('sub_branch_id', $floor->sub_branch_id)
                                 ->where('id', '!=', $floor->id)
                                 ->whereNull('deleted_at');
                })
            ],
            'description' => 'sometimes|nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'floor_number.unique' => 'Ya existe otro piso con este número en la misma sub sucursal.',
            'floor_number.min' => 'El número de piso debe ser mayor o igual a 0.',
        ];
    }
}
