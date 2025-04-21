<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiagnosisRequest;
use App\Http\Requests\PrescriptionRequest;
use App\Http\Requests\PatientRecordRequest;
use App\Http\Resources\DiagnosisResource;
use App\Http\Resources\PrescriptionResoure;
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
        return Doctor::where('name', $doctor_name)->value('id');
    }

    public function getPatientIdByName($patient_name)
    {
        return Patient::where('name', $patient_name)->value('id');
    }


    public function getdiagnosisByName($diseases_name)
    {

        $diagnosis = Diagnosis::where('diseases_name', $diseases_name)->first(['id', 'doctor_id', 'patient_id']);
        if (!$diagnosis) {
            return null;
        }

        return [
            'id' => $diagnosis->id,
            'd_doctor_id' => $diagnosis->doctor_id,
            'd_patient_id' => $diagnosis->patient_id
        ];

    }

    public function getprescriptionByName($medication_name)
    {


        $prescription = prescription::where('medication_name', $medication_name)->first(['id', 'doctor_id', 'patient_id']);
        if (!$prescription) {
            return null;
        }

        return [
            'id' => $prescription->id,
            'p_doctor_id' => $prescription->doctor_id,
            'p_patient_id' => $prescription->patient_id
        ];
    }


    public function CreatePatientRecord(PatientRecordRequest $request)
    {


        $doctor_id = $this->getDoctorIdByName($request->doctor_name);
        $patient_id = $this->getPatientIdByName($request->patient_name);
        $diagnosis_model = $this->getdiagnosisByName($request->diseases_name);
        $prescription_model = $this->getprescriptionByName($request->medication_name);

        // if (!$doctor_id || !$patient_id || !$diagnosis_model || !$prescription_model) {
        //     return response()->json(['message' => 'Doctor, Patient, Diagnosis, or Prescription not found'], 404);
        // }

        //more dynamic 
        if (!$doctor_id) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        if (!$patient_id) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        if (!$diagnosis_model) {
            return response()->json(['message' => 'Diagnosis not found'], 404);
        }

        if (!$prescription_model) {
            return response()->json(['message' => 'Prescription not found'], 404);
        }



        if (// another check
            $doctor_id !== $diagnosis_model['d_doctor_id'] && $patient_id !== $diagnosis_model['d_patient_id'] &&
            $doctor_id !== $prescription_model['p_doctor_id'] && $patient_id !== $prescription_model['p_patient_id']
        ) {
            return response()->json([
                'message' => 'Doctor or Patient mismatch with Diagnosis or Prescription record'
            ], 400);
        }
        $record = PatientRecord::create([
            'doctor_id' => $doctor_id,
            'patient_id' => $patient_id,
            'diagnosis_id' => $diagnosis_model['id'],
            'prescription_id' => $prescription_model['id']
        ]);

        return response()->json([
            'message' => 'Patient Record Created Successfully',
            'record' => $record
        ], 201);


    }



    public function getPatientRecord($patient_name)
    {//doctor 
        $patient = Patient::find($patient_name);

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

    public function Diagnosis(DiagnosisRequest $request)
    {//doctor


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

        return response()->json(['message' => 'Diagnosis Added Successfully', 'diagnosis' => new DiagnosisResource($diagnosis)], 200);
    }



    public function Prescription(PrescriptionRequest $request)
    {//doctor

        $doctor_id = $this->getDoctorIdByName($request->doctor_name);
        $patient_id = $this->getPatientIdByName($request->patient_name);
        $diagnosis_model = $this->getdiagnosisByName($request->diagnosis_name);
        if (!is_array($diagnosis_model)) {
            return response()->json(['message' => 'Diagnosis not found'], 404);
        }
        if (!$doctor_id) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        if (!$patient_id) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        $prescription = Prescription::create([
            'medication_name' => $request->medication_name,
            'dosage_amount' => $request->dosage_amount,
            'frequency' => $request->frequency,
            'duration' => $request->duration,
            'diagnosis_id' => $diagnosis_model['id'],
            'doctor_id' => $doctor_id,
            'patient_id' => $patient_id,
        ]);

        // $full_diagnosis = Diagnosis::find($diagnosis_model['id']);

        // if (!$full_diagnosis) {
        //     return response()->json(['message' => 'Diagnosis details not found'], 404);
        // }

        // $prescription->load('diagnosis');



        return response()->json([
            'message' => 'Prescription Added Successfully',
            'prescription' => new PrescriptionResoure($prescription->load('diagnosis')),
        ], 200);
    }

}
