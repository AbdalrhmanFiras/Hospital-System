<?php

namespace App\Models;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Patient;
use App\Models\PatientRecord;
use App\Models\Diagnosis;
use App\Models\prescription;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Doctor extends Authenticatable implements MustVerifyEmail
{// ok

    use Notifiable;


    protected $fillable = [
        'name',
        'Specialization',
        'Degree',
        'Available',
        'phone',
        'email',
        'price',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
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
