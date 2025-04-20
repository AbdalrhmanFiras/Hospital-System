<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function DoctorSignin(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'Specialization' => 'required|string',
            'Degree' => 'required|string|in:Bachelor,Master,Doctoral',
            'Available' => 'required|array',//
            'Available.*' => 'required|string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'phone' => 'required|string|min:10',
            'email' => 'nullable|string|email|unique'
        ]);

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
