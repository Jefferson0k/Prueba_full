<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'min:2', 'max:100'],
            'category_id'     => ['required', 'exists:product_categories,id'],
            'is_active'       => ['required', 'boolean'],
            'price'  => ['required', 'numeric', 'min:0'],
            'description'     => ['nullable', 'string', 'max:1000'],
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
            'price.required'          => 'Price is required.',
            'price.numeric'           => 'Price must be a number.',
            'purchase_price.min'      => 'Purchase price cannot be negative.',
            'description.string'      => 'Description must be a string.',
            'description.max'         => 'Description must not exceed 1000 characters.',
        ];
    }
}
