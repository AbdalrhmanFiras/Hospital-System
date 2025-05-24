<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Patient extends Model
{// ok
    use Notifiable;

    protected $fillable = ['name', 'address', 'age', 'phone', 'email', 'gender'];
    public function Doctors(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function PatientRecords(): HasMany
    {
        return $this->hasMany(PatientRecord::class);
    }

    public function Appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
