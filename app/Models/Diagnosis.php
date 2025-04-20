<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Patient;
use App\Models\Doctor;

class Diagnosis extends Model
{

    protected $fillable = ['diseases_name', 'diseases', 'diagnoses', 'allergies', 'doctor_name', 'patient_name', 'doctor_id', 'patient_id'];
    public function Doctors(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }


    public function Patients(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
