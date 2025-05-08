<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentHistory extends Model
{
    protected $fillable = [
        'patient_record_id',
        'appointment_date',
        'appointment_time',
    ];
}
