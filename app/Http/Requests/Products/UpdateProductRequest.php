<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'min:2', 'max:100'],
            'category_id'    => ['required', 'exists:product_categories,id'],
            'is_active'      => ['required', 'boolean'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price'     => ['required', 'numeric', 'min:0'],
            'unit_type'      => ['required', 'in:piece,bottle,pack,kg,liter'],
            'description'    => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'Name is required.',
            'name.min'                => 'Name must be at least 2 characters.',
            'name.max'                => 'Name must not exceed 100 characters.',
            'category_id.required'    => 'Category is required.',
            'category_id.exists'      => 'Selected category does not exist.',
            'is_active.required'      => 'Status is required.',
            'is_active.boolean'       => 'Status must be true or false.',
            'purchase_price.required' => 'Purchase price is required.',
            'purchase_price.numeric'  => 'Purchase price must be a number.',
            'purchase_price.min'      => 'Purchase price cannot be negative.',
            'sale_price.required'     => 'Sale price is required.',
            'sale_price.numeric'      => 'Sale price must be a number.',
            'sale_price.min'          => 'Sale price cannot be negative.',
            'unit_type.required'      => 'Unit type is required.',
            'unit_type.in'            => 'Selected unit type is invalid.',
            'description.string'      => 'Description must be a string.',
            'description.max'         => 'Description must not exceed 1000 characters.',
        ];
    }
}
