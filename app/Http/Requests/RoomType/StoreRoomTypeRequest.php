<?php

namespace App\Http\Requests\RoomType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo usuarios autenticados pueden registrar tipos de habitación
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:room_types,name',
            'description' => 'nullable|string|max:500',
            'capacity' => 'required|integer|min:1',
            'base_price_per_hour' => 'required|numeric|min:0',
            'base_price_per_day' => 'required|numeric|min:0',
            'base_price_per_night' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del tipo de habitación es obligatorio.',
            'name.unique' => 'Ya existe un tipo de habitación con este nombre.',
            'capacity.required' => 'La capacidad es obligatoria.',
            'capacity.integer' => 'La capacidad debe ser un número entero.',
            'base_price_per_hour.required' => 'Debe indicar un precio base por hora.',
            'base_price_per_day.required' => 'Debe indicar un precio base por día.',
            'base_price_per_night.required' => 'Debe indicar un precio base por noche.',
        ];
    }
}
