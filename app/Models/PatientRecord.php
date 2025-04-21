<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Patient;
use App\Models\Doctor;

use App\Models\Diagnosis;
use App\Models\prescription;
class PatientRecord extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'diagnosis_id',
        'prescription_id',

    ];
    public function doctors(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function Patinets(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }// work

    // public function Diagnosises(): HasMany
    // {
    //     return $this->hasMany(Diagnosis::class);
    // }

    // public function Prescriptions(): HasMany
    // {
    //     return $this->hasMany(Prescription::class);
    // }


    public function diagnosis()
    {
        return $this->belongsTo(Diagnosis::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }


}
