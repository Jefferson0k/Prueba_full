<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRoomRequest extends FormRequest{
    public function authorize(): bool{
        return $this->user()->can('create', \App\Models\Room::class);
    }
    public function rules(): array{
        return [
            'floor_id' => 'required|uuid|exists:floors,id',
            'room_type_id' => 'required|uuid|exists:room_types,id',
            'room_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rooms')->where(function ($query) {
                    return $query->where('floor_id', $this->floor_id)
                                 ->whereNull('deleted_at');
                })
            ],
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => ['nullable', Rule::in(['available', 'maintenance'])],
            'is_active' => 'nullable|boolean',
        ];
    }
    public function messages(): array{
        return [
            'floor_id.required' => 'El piso es requerido.',
            'floor_id.exists' => 'El piso seleccionado no existe.',
            'room_type_id.required' => 'El tipo de habitación es requerido.',
            'room_type_id.exists' => 'El tipo de habitación seleccionado no existe.',
            'room_number.required' => 'El número de habitación es requerido.',
            'room_number.unique' => 'Ya existe una habitación con este número en el piso seleccionado.',
            'room_number.max' => 'El número de habitación no puede exceder 50 caracteres.',
        ];
    }
}