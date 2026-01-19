<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientNotification extends Model
{
    protected $fillable = [
        'patient_id',
        'channel',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
