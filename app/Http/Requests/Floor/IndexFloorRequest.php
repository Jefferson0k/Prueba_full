<?php

namespace App\Http\Requests\Floor;

use App\Models\Floor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexFloorRequest extends FormRequest{
    public function authorize(): bool{
        return $this->user()->can('viewAny', Floor::class);
    }
    public function rules(): array{
        return [
            'sub_branch_id' => 'nullable|uuid|exists:sub_branches,id',
            'is_active' => 'nullable|boolean',
            'search' => 'nullable|string|max:255',
            'sort_by' => ['nullable', Rule::in(['name', 'floor_number', 'created_at', 'updated_at'])],
            'sort_order' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => 'nullable|integer|min:1|max:100',
            'with_room_counts' => 'nullable|boolean',
        ];
    }
    protected function prepareForValidation(): void{
        if ($this->has('with_room_counts')) {
            $this->merge([
                'with_room_counts' => filter_var(
                    $this->input('with_room_counts'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ),
            ]);
        }
    }
}
