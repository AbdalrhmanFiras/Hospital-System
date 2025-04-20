<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Patient;
use App\Models\Doctor;

class Diagnosis extends Model
{
    public function Doctors(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }


    public function Patients(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
