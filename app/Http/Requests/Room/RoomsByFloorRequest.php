<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomsByFloorRequest extends FormRequest{
    public function authorize(): bool{
        return $this->user()->can('viewAny', \App\Models\Room::class);
    }
    public function rules(): array{
        return [
            'status' => ['nullable', Rule::in(['available', 'occupied', 'maintenance', 'cleaning'])],
            'include_inactive' => 'nullable|boolean',
            'room_type_id' => 'nullable|uuid|exists:room_types,id',
        ];
    }
    public function messages(): array{
        return [
            'room_type_id.exists' => 'El tipo de habitación seleccionado no existe.',
            'status.in' => 'El estado debe ser: available, occupied, maintenance o cleaning.',
        ];
    }
}
