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
            'page' => 'integer|min:1',
            'pageSize' => 'integer|min:1|max:100',
            'search' => 'string|nullable',
            'sortBy' => 'string|nullable',
        ];
    }

    public function validatedData(): array
    {
        return [
            'page' => $this->input('page', 1),
            'pageSize' => $this->input('pageSize', 10),
            'search' => $this->input('search', ''),
            'sortBy' => $this->input('sortBy', 'first_name'),
        ];
    }
}
