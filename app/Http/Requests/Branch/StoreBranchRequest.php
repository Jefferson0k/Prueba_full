<?php

namespace App\Http\Requests\Branch;

use App\Models\Branch;
use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Branch::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ];
    }
}
