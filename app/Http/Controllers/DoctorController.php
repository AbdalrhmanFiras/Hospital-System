<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{

    public function getPatientRecord($doctor_id)
    {
        $doctor = Doctor::find($doctor_id);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $records = $doctor->PatientRecords;

        if ($records->isEmpty()) {
            return response()->json(['message' => 'Doctor has no patient records'], 404);
        }

        return response()->json(['records' => $records], 200);
    }
}
