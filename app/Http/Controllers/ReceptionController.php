<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Diagnosis;
use App\Models\Doctor;
use App\Models\PatientRecord;
use App\Models\prescription;
use Illuminate\Http\Request;

class ReceptionController extends Controller
{


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

    //     if (!$doctor_id || !$patient_id || !$diagnosis_model || !$prescription_model) {
    //         return response()->json(['message' => 'Doctor, Patient, Diagnosis, or Prescription not found'], 404);
    //     }


    //     if (
    //         $doctor_id == $diagnosis_model['d_doctor_id'] && $patient_id == $diagnosis_model['d_patient_id'] &&
    //         $doctor_id == $prescription_model['p_doctor_id'] && $patient_id == $prescription_model['p_patient_id']
    //     ) {
    //         $record = PatientRecord::create([
    //             'doctor_id' => $doctor_id,
    //             'patient_id' => $patient_id,
    //             'diagnosis_id' => Diagnosis::where('diseases_name', $request->diseases_name)->value('id'),
    //             'prescription_id' => prescription::where('medication_name', $request->medication_name)->value('id')
    //         ]);



    //         return response()->json([
    //             'message' => 'Patient Record Created Successfully',
    //             'record' => $record
    //         ], 201);

    //     }

    // }

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


        $prescription = Prescription::where('medication_name', $medication_name)->first(['id', 'doctor_id', 'patient_id']);
        if (!$prescription) {
            return null;
        }

        return [
            'id' => $prescription->id,
            'p_doctor_id' => $prescription->doctor_id,
            'p_patient_id' => $prescription->patient_id
        ];
    }

    ////////////////////////////////////////////// From DoctorController //////////////////////////////////




    public function getAllPatientRecord($doctor_name)
    {// it should be i another controller 
        $doctor_id = Doctor::where('Name', $doctor_name)->first('id');



        if (!$doctor_id) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $records = $doctor_id->PatientRecords;

        if ($records->isEmpty()) {
            return response()->json(['message' => 'Doctor has no patient records'], 404);
        }

        return response()->json(['records' => $records], 200);
    }




















    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function AddPatient(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer',
            'address' => 'required|string',
            'phone' => 'required|string|min:7',
            'email' => 'nullable|email|unique',
            'gender' => 'required|string|in:male,female'
        ]);


        $patient = Patient::create([
            'name' => $request->name,
            'age' => $request->age,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'gender' => $request->gender
        ]);


        return response()->json([
            'message' => 'patient loggin successfully',
            'patient' => $patient
        ], 200);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
