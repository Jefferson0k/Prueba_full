<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return [
            'document_number' => 'required|string|max:20',
            'name' => 'required|string|max:255',
        ];
    }
    public function messages(): array{
        return [
            'document_number.required' => 'El número de documento es obligatorio.',
            'name.required' => 'El nombre del cliente es obligatorio.',
        ];
    }
}
