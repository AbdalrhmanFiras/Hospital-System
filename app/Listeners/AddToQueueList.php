<?php
namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Models\QueueEntry;
use Illuminate\Support\Facades\Log;

class AddToQueueList
{
    public function handle(AppointmentCreated $event)
    {
        $appointment = $event->appointment;
        $appointment->load('patient');


        QueueEntry::create([

            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient->id,
            'patient_name' => $appointment->patient->name,

        ]);


    }
}