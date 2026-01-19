<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z]+$/',
            ],
            'last_name' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z]+$/',
            ],
            'email' => [
                'sometimes',
                'email',
                'ends_with:@gmail.com',
                Rule::unique('patients', 'email')->ignore($this->patient),
            ],
            'country_iso' => [
                'sometimes',
                'string',
                'size:2',
            ],
            'phone_number' => [
                'sometimes',
                'digits_between:6,15',
            ],
            'document_image' => [
                'sometimes',
                'image',
                'mimes:jpg,jpeg',
                'max:2048',
            ],
        ];
    }
}
