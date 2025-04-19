<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class prescription extends Model
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
