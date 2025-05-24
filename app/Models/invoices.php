<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InteractsWithQueue;

class Invoices extends Model
{


    protected $fillable = [
        'patient_id',
        'appointment_id',
        'invoice_number',
        'invoice_date',
        'total_amount',
        'status',
    ];
    public function Appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}

