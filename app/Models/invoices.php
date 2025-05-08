<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class invoices extends Model
{


    protected $fillable = [
        'patient_id',
        'appointment_id', // Foreign key linking to appointments table
        'invoice_number',
        'invoice_date',
        'total_amount',
        'status',
    ];
    public function Appointment(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
