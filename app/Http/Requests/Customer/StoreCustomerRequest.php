<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Permite que cualquier usuario autorizado use esta solicitud
        return true;
    }

    public function rules(): array
    {
        return [
            'document_number' => 'required|string|max:20|unique:customers,document_number',
            'name' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.unique' => 'El número de documento ya está registrado.',
            'name.required' => 'El nombre del cliente es obligatorio.',
        ];
    }
}
