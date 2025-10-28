<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class FinishBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Pagos adicionales (si hay saldo pendiente)
            'payments' => 'nullable|array',
            'payments.*.payment_method_id' => 'required|uuid|exists:payment_methods,id',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.cash_register_id' => 'required|uuid|exists:cash_registers,id',
            'payments.*.operation_number' => 'nullable|string|max:100',
            
            // Notas opcionales
            'notes' => 'nullable|string|max:1000',
            
            // Forzar check-out aunque haya deuda (solo admin)
            'force_checkout' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'payments.*.payment_method_id.required' => 'El método de pago es obligatorio',
            'payments.*.amount.min' => 'El monto del pago debe ser mayor a 0',
            'payments.*.cash_register_id.required' => 'La caja registradora es obligatoria',
        ];
    }
}