<?php

namespace App\Http\Requests\Movement;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'          => ['required', 'string', 'unique:movements,code'],
            'date'          => ['required', 'date'],

            'provider_id'   => ['required', 'exists:providers,id'],

            'payment_type'  => ['required', 'in:credito,contado'],
            'credit_date'   => ['required_if:payment_type,credito', 'nullable', 'date'],
            'includes_igv'  => ['required', 'boolean'],
            'voucher_type'  => ['required', 'in:factura,boleta,otros'],

            'created_by'    => ['nullable', 'exists:users,id'],
            'updated_by'    => ['nullable', 'exists:users,id'],
            'deleted_by'    => ['nullable', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'          => 'El código del movimiento es obligatorio.',
            'code.unique'            => 'Este código ya existe.',

            'date.required'          => 'La fecha es obligatoria.',
            'date.date'              => 'Debe ser una fecha válida.',

            'provider_id.required'   => 'El proveedor es obligatorio.',
            'provider_id.exists'     => 'El proveedor seleccionado no existe.',

            'payment_type.required'  => 'El tipo de pago es obligatorio.',
            'payment_type.in'        => 'El tipo de pago debe ser "credito" o "contado".',

            'credit_date.required_if'=> 'La fecha de crédito es obligatoria cuando el pago es a crédito.',
            'credit_date.date'       => 'La fecha de crédito debe ser una fecha válida.',

            'includes_igv.required'  => 'Debe indicar si incluye IGV.',
            'includes_igv.boolean'   => 'El valor de IGV debe ser verdadero o falso.',

            'voucher_type.required'  => 'El tipo de comprobante es obligatorio.',
            'voucher_type.in'        => 'El tipo de comprobante debe ser "factura", "boleta" u "otros".',

            'created_by.exists'      => 'El usuario creador no existe.',
            'updated_by.exists'      => 'El usuario que actualiza no existe.',
            'deleted_by.exists'      => 'El usuario que elimina no existe.',
        ];
    }
}
