<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRoomRequest extends FormRequest{
    public function authorize(): bool{
        return $this->user()->can('viewAny', \App\Models\Room::class);
    }
    public function rules(): array{
        return [
            'branch_id' => 'nullable|uuid|exists:branches,id',
            'sub_branch_id' => 'nullable|uuid|exists:sub_branches,id',
            'floor_id' => 'nullable|uuid|exists:floors,id',
            'status' => ['nullable', Rule::in(['available', 'occupied', 'maintenance', 'cleaning'])],
            'room_type_id' => 'nullable|uuid|exists:room_types,id',
            'is_active' => 'nullable|boolean',
            'search' => 'nullable|string|max:255',
            'sort_by' => ['nullable', Rule::in(['room_number', 'name', 'status', 'created_at', 'updated_at'])],
            'sort_order' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
    public function messages(): array{
        return [
            'sub_branch_id.exists' => 'La sub sucursal seleccionada no existe.',
            'floor_id.exists' => 'El piso seleccionado no existe.',
            'room_type_id.exists' => 'El tipo de habitación seleccionado no existe.',
            'status.in' => 'El estado debe ser: available, occupied, maintenance o cleaning.',
            'per_page.max' => 'No se pueden mostrar más de 100 registros por página.',
        ];
    }
}