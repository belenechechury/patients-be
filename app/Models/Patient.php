<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory;

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
