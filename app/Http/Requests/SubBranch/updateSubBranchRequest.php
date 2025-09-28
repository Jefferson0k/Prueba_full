<?php

namespace App\Http\Requests\SubBranch;

use App\Models\SubBranch;
use Illuminate\Foundation\Http\FormRequest;

class updateSubBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', SubBranch::class);
    }

    public function rules(): array
    {
        return [
            
        ];
    }
}
