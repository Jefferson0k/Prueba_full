<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;

class RoomStatsRequest extends FormRequest{
    public function authorize(): bool{
        return $this->user()->can('viewAny', \App\Models\Room::class);
    }
    public function rules(): array{
        return [
            'sub_branch_id' => 'nullable|uuid|exists:sub_branches,id',
            'floor_id' => 'nullable|uuid|exists:floors,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ];
    }
    public function messages(): array{
        return [
            'sub_branch_id.exists' => 'La sub sucursal seleccionada no existe.',
            'floor_id.exists' => 'El piso seleccionado no existe.',
            'date_to.after_or_equal' => 'La fecha final debe ser mayor o igual a la fecha inicial.',
        ];
    }
}
