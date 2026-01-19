<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListPatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'page_size' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string',
            'sort_by' => 'sometimes|string',
            'created_from' => 'sometimes|date',
            'created_to' => 'sometimes|date',
        ];
    }

    public function validatedData(): array
    {
        return array_merge([
            'page' => 1,
            'page_size' => 10,
            'search' => '',
            'sort_by' => 'first_name',
            'created_from' => null,
            'created_to' => null,
        ], $this->validated());
    }
}
