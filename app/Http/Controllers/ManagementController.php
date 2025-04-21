<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRequest;
use App\Models\Doctor;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function DoctorSignin(DoctorRequest $request)
    {


        $doctor = Doctor::create([
            'name' => $request->name,
            'Specialization' => $request->Specialization,
            'Degree' => $request->Degree,
            'Available' => json_encode($request->Available), // Store as JSON
            // mysql dont accept array soi convert it into string
            'phone' => $request->phone,
            'email' => $request->email
        ]);

        return response()->json(['message' => 'Doctor signin successfully', 'doctor' => $doctor], 200);


    }
}
