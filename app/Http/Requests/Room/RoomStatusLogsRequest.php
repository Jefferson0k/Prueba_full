<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomStatusLogsRequest extends FormRequest{
    public function authorize(): bool{
        return $this->user()->can('view', $this->route('room'));
    }
    public function rules(): array{
        return [
            'per_page' => 'nullable|integer|min:1|max:50',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'status' => ['nullable', Rule::in(['available', 'occupied', 'maintenance', 'cleaning'])],
        ];
    }
    public function messages(): array{
        return [
            'to_date.after_or_equal' => 'La fecha final debe ser mayor o igual a la fecha inicial.',
            'per_page.max' => 'No se pueden mostrar más de 50 registros por página.',
        ];
    }
}
