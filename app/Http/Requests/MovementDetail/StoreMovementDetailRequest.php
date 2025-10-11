<?php

namespace App\Http\Requests\MovementDetail;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'movement_id'   => 'required|uuid|exists:movements,id',
            'product_id'    => 'required|uuid|exists:products,id',
            'unit_price'    => 'required|numeric|min:0',
            'boxes'         => 'required|integer|min:0',
            'units_per_box' => 'required|integer|min:1',
            'expiry_date'   => 'nullable|date|after:today',
        ];
    }

    public function messages(): array
    {
        return [
            'movement_id.required' => 'El movimiento es obligatorio.',
            'movement_id.exists'   => 'El movimiento seleccionado no existe.',
            'product_id.required'  => 'El producto es obligatorio.',
            'product_id.exists'    => 'El producto seleccionado no existe.',
            'unit_price.required'  => 'El precio unitario es obligatorio.',
            'boxes.required'       => 'La cantidad de cajas es obligatoria.',
            'units_per_box.required' => 'Las unidades por caja son obligatorias.',
        ];
    }
}
