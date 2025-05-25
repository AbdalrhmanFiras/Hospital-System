<?php

namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Models\Invoices;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Models\Doctor;
class InvoiceAdd implements ShouldQueue
{
    public function handle(AppointmentCreated $event)
    {
        try {
            $appointment = $event->appointment;
            $appointment->load(['patient', 'doctor']);

            DB::transaction(function () use ($appointment) {
                $lastInvoiceNumber = Invoices::lockForUpdate()->max('invoice_number') ?? 0;
                $newInvoiceNumber = $lastInvoiceNumber + 1;

                Invoices::create([
                    'patient_id' => $appointment->patient->id,
                    'appointment_id' => $appointment->id,
                    'invoice_number' => $newInvoiceNumber,
                    'invoice_date' => now(),
                    'total_amount' => $appointment->doctor->price
                ]);
            });

        } catch (\Exception $e) {
            \Log::error('Invoice creation failed: ' . $e->getMessage(), [
                'appointment_id' => $appointment->id ?? 'unknown'
            ]);

            throw $e;
        }
    }
}

