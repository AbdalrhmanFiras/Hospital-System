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
    public function getDoctorIdByName($doctor_name)
    {
        return Doctor::where('name', $doctor_name)->value('id');
    }

    public function getPatientIdByName($patient_name)
    {
        return Patient::where('name', $patient_name)->value('id');

    }
    // public function FindDoctorbyname($doctor_name)
    // {

    //     $doctor_id = Doctor::when($doctor_name, function ($query, $doctor_name) {
    //         return $query->where('name', $doctor_name);
    //     })->value('id');

    //     if (!$doctor_id) {
    //         return response()->json(['message' => 'there is no doctor like this name']);
    //     }

    //     return response()->json(['doctor_id' => $doctor_id]);
    // }

    public function Diagnosis(Request $request)
    {
        $request->validate([
            'diseases_name' => 'required|string',
            'diseases' => 'required|string|in:infectious diseases, deficiency diseases, hereditary diseases, physiological diseases',
            'diagnoses' => 'required|string|in:clinical,medical',
            'allergies' => 'nullable|text',
            'doctor_name' => 'required|string',
            'patient_name' => 'required|string',
        ]);

        $doctor_id = $this->getDoctorIdByName($request->doctor_name);
        $patient_id = $this->getPatientIdByName($request->patient_name);

        if (!$doctor_id || !$patient_id) {
            return response()->json(['message' => 'Doctor or Patient not found'], 404);
        }

        $diagnosis = Diagnosis::create([
            'diseases_name' => $request->diseases_name,
            'diseases' => $request->diseases,
            'diagnoses' => $request->diagnoses,
            'allergies' => $request->allergies,
            'doctor_id' => $doctor_id,
            'patient_id' => $patient_id,
        ]);

        return response()->json(['message' => 'Diagnosis Added Successfully', 'diagnosis' => $diagnosis], 200);
    }



}
