<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Patient extends Model
{
    public function Doctors(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
