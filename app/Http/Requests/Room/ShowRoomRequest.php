<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;

class ShowRoomRequest extends FormRequest{
    public function authorize(): bool{
        return $this->user()->can('view', $this->route('room'));
    }

    public function rules(): array
    {
        return [
            'include' => 'nullable|string',
        ];
    }
}