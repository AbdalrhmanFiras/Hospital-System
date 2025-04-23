<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = ['doctor_id', 'patient_id', 'appointment_date', 'appointment_time'];

    public function Doctors(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function Patients(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
