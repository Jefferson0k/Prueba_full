<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeRoomStatusRequest extends FormRequest{
    public function authorize(): bool{
        return $this->user()->can('update', $this->route('room'));
    }
    public function rules(): array{
        return [
            'new_status' => [
                'required',
                Rule::in(['available', 'occupied', 'maintenance', 'cleaning'])
            ],
            'reason' => 'nullable|string|max:500',
        ];
    }
    public function messages(): array{
        return [
            'new_status.required' => 'El nuevo estado es requerido.',
            'new_status.in' => 'El estado debe ser: available, occupied, maintenance o cleaning.',
            'reason.max' => 'La razón no puede exceder 500 caracteres.',
        ];
    }
    public function withValidator($validator): void{
        $validator->after(function ($validator) {
            $room = $this->route('room');
            $newStatus = $this->new_status;
            
            if ($room && $room->status === $newStatus) {
                $validator->errors()->add('new_status', 'La habitación ya tiene el estado solicitado.');
                return;
            }

            if ($room && !$this->isValidStatusTransition($room->status, $newStatus)) {
                $validator->errors()->add('new_status', 'No se puede cambiar de ' . $room->status . ' a ' . $newStatus);
            }
        });
    }
    private function isValidStatusTransition(string $currentStatus, string $newStatus): bool{
        $allowedTransitions = [
            'available' => ['occupied', 'maintenance', 'cleaning'],
            'occupied' => ['available', 'cleaning', 'maintenance'],
            'maintenance' => ['available', 'cleaning'],
            'cleaning' => ['available', 'maintenance'],
        ];
        return in_array($newStatus, $allowedTransitions[$currentStatus] ?? []);
    }
}