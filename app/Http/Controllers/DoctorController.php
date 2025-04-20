<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;

class DoctorController extends Controller
{


    public function getAllPatientRecord($doctor_id)
    {// it should be i another controller 
        $doctor = Doctor::find($doctor_id);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $records = $doctor->PatientRecords;

        if ($records->isEmpty()) {
            return response()->json(['message' => 'Doctor has no patient records'], 404);
        }

        return response()->json(['records' => $records], 200);
    }//ok

    public function getPatientRecord($patient_id)
    {
        $patient = Patient::find($patient_id);

        if (!$patient) {
            return response()->json(['message' => 'patient not found'], 404);
        }

        $records = $patient->PatientRecords;

        if ($records->isEmpty()) {
            return response()->json(['message' => 'patient has no patient records'], 404);
        }

        return response()->json(['records' => $records], 200);
    }

    // public function CreatePatientRecord(Request $request)
    // {
    //     $request->validate([
    //         'doctor_id' => 'required|string|exists:doctors,id'

    //     ]);

    //     // doctor_id , patient_id , diagnosis_id , preseciton_id

    // }

    public function Diagnosis(Request $request)
    {
        $request->validate([
            'diseases_name' => 'required|string',
            'diseases' => 'required|string|in:infectious diseases, deficiency diseases, hereditary diseases, physiological diseases',
            'diagnoses' => 'required|string|in:clinical,medical',
            'allergies' => 'nullable|text',
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:patients,id'
        ]);


        $diagnosis = Diagnosis::create([
            'diseases_name' => $request->diseases_name,
            'diseases' => $request->diseases,
            'diagnoses' => $request->diagnoses,
            'allergies' => $request->allergies,
            'doctor_id' => $request->doctor_id,
            'patient_id' => $request->patient_id,
        ]);


        return response()->json(['message' => 'Diagnosis Add Successfully', 'diagnosis' => $diagnosis], 200);

    }




}
