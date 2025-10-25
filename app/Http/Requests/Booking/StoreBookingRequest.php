<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Si usas autenticación, puedes validar permisos aquí
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => 'required|exists:rooms,id',
            'customers_id' => 'required|exists:customers,id',
            'rate_type_id' => 'required|exists:rate_types,id',
            'currency_id' => 'required|exists:currencies,id',
            'check_in' => 'required|date',
            'total_hours' => 'required|integer|min:1',
            'rate_per_hour' => 'required|numeric|min:0',
            'voucher_type' => 'sometimes|in:ticket,boleta,factura',

            // Pagos
            'payments' => 'required|array|min:1',
            'payments.*.payment_method_id' => 'required|exists:payment_methods,id',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.cash_register_id' => 'nullable|exists:cash_registers,id',
            'payments.*.operation_number' => 'nullable|string',

            // Productos opcionales
            'consumptions' => 'sometimes|array',
            'consumptions.*.product_id' => 'required|exists:products,id',
            'consumptions.*.quantity' => 'required|integer|min:1',
            'consumptions.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.required' => 'La habitación es obligatoria.',
            'payments.required' => 'Debe registrar al menos un pago.',
            'consumptions.*.product_id.exists' => 'El producto no existe en el sistema.',
        ];
    }
}
