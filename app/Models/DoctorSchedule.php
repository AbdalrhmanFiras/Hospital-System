<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSchedule extends Model
{
    protected $fillable = ['doctor_id', 'start_time', 'end_time', 'days_off', 'day_of_week'];
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
