<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientRecord;
use App\Models\prescription;
use Illuminate\Http\Request;

class DoctorController extends Controller
{

    public function getDoctorIdByName($doctor_name)
    {
        $doctor = Doctor::where('name', $doctor_name)->value('id');
        if (!$doctor) {
            return response()->json(['message' => 'patient not found'], 404);
        }

        return $doctor;
    }


    public function getPatientIdByName($patient_name)
    {
        $patient = Patient::where('name', $patient_name)->value('id');

        if (!$patient) {
            return response()->json(['message' => 'patient not found'], 404);
        }

        return $patient;
    }

    public function getdiagnosisByName($diseases_name)
    {


        $diagnosis = Diagnosis::where('diseases_name', $diseases_name)->first(['id', 'doctor_id', 'patient_id']);


        // first() : grab me the full model

        if (!$diagnosis) {
            return response()->json(['Diagnosis not found'], 404);
        }

        $diagnosis_doctor_id = $diagnosis->doctor_id;
        $diagnosis_patient_id = $diagnosis->patient_id;

        return [
            'd_doctor_id' => $diagnosis_doctor_id,
            'd_patient_id' => $diagnosis_patient_id
        ];

    }

    public function getprescriptionByName($medication_name)
    {

        $Prescription = prescription::where('medication_name', $medication_name)->first(['id', 'doctor_id', 'patient_id']);

        $prescription_doctor_id = $Prescription->doctor_id;
        $prescription_patient_id = $Prescription->patient_id;

        return [
            'p_doctor_id' => $prescription_doctor_id,
            'p_patient_id' => $prescription_patient_id
        ];
    }


    public function CreatePatientRecord(Request $request)
    {
        $request->validate([
            'doctor_name' => 'required|string',
            'patient_name' => 'required|string',
            'diseases_name' => 'required|string',
            'medication_name' => 'required|string'
        ]);


        $doctor_id = $this->getDoctorIdByName($request->doctor_name);
        $patient_id = $this->getPatientIdByName($request->patient_name);
        $diagnosis_model = $this->getdiagnosisByName($request->diseases_name);
        $prescription_model = $this->getprescriptionByName($request->medication_name);

        if (!$doctor_id || !$patient_id || !$diagnosis_model || !$prescription_model) {
            return response()->json(['message' => 'Doctor, Patient, Diagnosis, or Prescription not found'], 404);
        }


        if (
            $doctor_id == $diagnosis_model['d_doctor_id'] && $patient_id == $diagnosis_model['d_patient_id'] &&
            $doctor_id == $prescription_model['p_doctor_id'] && $patient_id == $prescription_model['p_patient_id']
        ) {
            $record = PatientRecord::create([
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
                'diagnosis_id' => Diagnosis::where('diseases_name', $request->diseases_name)->value('id'),
                'prescription_id' => prescription::where('medication_name', $request->medication_name)->value('id')
            ]);



            return response()->json([
                'message' => 'Patient Record Created Successfully',
                'record' => $record
            ], 201);


        }


    }


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
    }

    public function getPatientRecord($patient_id)
    {//doctor 
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
    {//doctor
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


    public function Prescription(Request $request)
    {//doctor
        $request->validate([
            'medication_name' => 'required|string',
            'diagnosis_id' => 'required|string|exists:diagnoses,id',
            'dosage_amount' => 'nullable|string',
            'frequency' => 'nullable|string',
            'duration' => 'required|string',
            'doctor_name' => 'required|string',
            'patient_name' => 'required|string',
        ]);

        $doctor_id = $this->getDoctorIdByName($request->doctor_name);
        $patient_id = $this->getPatientIdByName($request->patient_name);

        if (!$doctor_id || !$patient_id) {
            return response()->json(['message' => 'Doctor or Patient not found'], 404);
        }

        if (!$patient_id) {
            return response()->json([
                'message' => 'Patient not found',
                'patient_name' => $request->patient_name,
                'patient_id' => $patient_id
            ], 404);
        }
        $prescription = Prescription::create([
            'medication_name' => $request->medication_name,
            'diagnosis_id' => $request->diagnosis_id,
            'dosage_amount' => $request->dosage_amount,
            'frequency' => $request->frequency,
            'duration' => $request->duration,
            'doctor_id' => $doctor_id,
            'patient_id' => $patient_id,
        ]);

        return response()->json([
            'message' => 'Prescription Added Successfully',
            'prescription' => $prescription
        ], 200);
    }

}
