<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class prescription extends Model
{
    protected $fillable = ['medication_name', 'doctor_name', 'patient_name', 'diagnosis_id', 'dosage_amount', 'frequency', 'duration', 'medication_id', 'doctor_id', 'patient_id', 'diagnosis_name', 'status'];

    public function Doctors(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function Patients(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function diagnosis()
    {
        return $this->belongsTo(Diagnosis::class);
    }
}
