<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z]+$/',
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z]+$/',
            ],
            'email' => [
                'required',
                'email',
                'ends_with:@gmail.com',
                'unique:patients,email',
            ],
            'country_iso' => [
                'required',
                'string',
                'size:2',
            ],
            'phone_number' => [
                'required',
                'digits_between:6,15',
            ],
            'document_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg',
                'max:2048',
            ],
        ];
    }
}
