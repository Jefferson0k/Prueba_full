<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest{
    public function authorize(): bool{
        return $this->user()->can('update', $this->route('room'));
    }
    public function rules(): array{
        $room = $this->route('room');
        
        return [
            'room_type_id' => 'sometimes|required|uuid|exists:room_types,id',
            'room_number' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('rooms')->where(function ($query) use ($room) {
                    return $query->where('floor_id', $room->floor_id)
                                 ->where('id', '!=', $room->id)
                                 ->whereNull('deleted_at');
                })
            ],
            'name' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ];
    }
    public function messages(): array{
        return [
            'room_type_id.exists' => 'El tipo de habitación seleccionado no existe.',
            'room_number.unique' => 'Ya existe otra habitación con este número en el mismo piso.',
            'room_number.max' => 'El número de habitación no puede exceder 50 caracteres.',
        ];
    }
}