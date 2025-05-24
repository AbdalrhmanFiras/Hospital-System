<?php

namespace App\Models;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\PatientRecord;
use App\Models\Diagnosis;
use App\Models\prescription;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Doctor extends Model// extends Authenticatable
{// ok

    protected $fillable = [
        'name',
        'Specialization',
        'Degree',
        'Available',
        'phone',
        'email',

    ];
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

    public function Appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function Schedule(): HasOne
    {
        return $this->hasOne(DoctorSchedule::class);
    }
}
