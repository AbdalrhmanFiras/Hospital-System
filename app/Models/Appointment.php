<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';


    protected $fillable = ['doctor_id', 'patient_id', 'appointment_date', 'appointment_time', 'doctor_name', 'patient_name', 'patients'];

    public function Doctors(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function Queue(): HasOne
    {
        return $this->hasOne(QueueEntry::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
