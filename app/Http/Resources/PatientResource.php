<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'country_iso' => $this->country_iso,
            'phone_number' => $this->phone_number,
            'document_image_path' => $this->document_image_path,
            'created_at' => $this->created_at,
        ];
    }
}
