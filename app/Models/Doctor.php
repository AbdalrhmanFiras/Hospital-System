<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\PatientRecord;
use App\Models\Diagnosis;
use App\Models\prescription;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Doctor extends Model
{// ok
    public function Patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function PatientRecords(): HasMany
    {
        return $this->hasMany(PatientRecord::class);
    }

    public function Diagnosises(): HasMany
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function Prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}
