<?php

namespace App\Http\Controllers;

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


    // public function CreateAppointment($)
}
