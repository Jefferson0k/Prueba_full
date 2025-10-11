<?php

namespace App\Http\Requests\MovementDetail;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovementDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
        ];
    }

    public function messages(): array
    {
        return [

        ];
    }
}
