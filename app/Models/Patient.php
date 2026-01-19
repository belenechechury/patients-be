<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'country_iso',
        'phone_number',
        'document_image_path',
    ];

    public function notifications()
    {
        return $this->hasMany(PatientNotification::class);
    }
}
