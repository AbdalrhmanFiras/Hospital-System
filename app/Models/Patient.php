<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Patient extends Model
{// ok

    protected $fillable = ['name', 'address', 'age', 'phone', 'email', 'gender'];
    public function Doctors(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function PatientRecords(): HasMany
    {
        return $this->hasMany(PatientRecord::class);
    }
}
