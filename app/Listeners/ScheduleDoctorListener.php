<?php

namespace App\Listeners;
use App\Models\Doctor;
use App\Events\DoctorCreate;
use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ScheduleDoctorListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DoctorCreate $event): void
    {
        $doctor = $event->doctor;

        $default = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday',];

        foreach ($default as $day) {
            DoctorSchedule::create([
                'doctor_id' => $doctor->id,
                'day_of_week' => $day,
                'start_time' => Carbon::parse('08:00'),
                'end_time' => Carbon::parse('17:00'),
                'days_off' => null
            ]);



        }
    }
}
