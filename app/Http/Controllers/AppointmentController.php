<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class AppointmentController extends Controller
{
    public function getDoctorBySplz(Request $request)
    {
        $doctors = Doctor::when($request->Specialization, function ($query, $Specialization) {
            return $query->where('Specialization', $Specialization);
        })->get();
        return response()->json(['message' => $doctors->isEmpty() ? 'There is no Doctor with this Specialization' : $doctors]);
    }


    public function CreateAppointment(CreateAppointmentRequest $request)
    {



        //false
        $isAvailable = !Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)->exists();
        //true
        if (!$isAvailable) {
            return response()->json(['message' => 'Time slot not available'], 409);
        }

        $appointment = Appointment::create($request->all());

        return response()->json(['message' => $appointment], 200);
    }
}
