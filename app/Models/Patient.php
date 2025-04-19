<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Patient extends Model
{// ok
    public function Doctors(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
