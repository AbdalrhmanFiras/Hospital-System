<?php

namespace App\Http\Controllers;
use App\Http\Requests\DiagnosisRequest;
use App\Http\Requests\PatientRequest;
use App\Http\Requests\PrescriptionRequest;
use App\Http\Requests\PatientRecordRequest;
use App\Http\Resources\DiagnosisResource;
use App\Http\Resources\PatientResource;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\PatientRecordResource;
use App\Http\Resources\PrescriptionResource;
use App\Models\Diagnosis;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientRecord;
use App\Models\prescription;
use Illuminate\Http\Request;
class ReceptionController extends Controller
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


    public function CreatePatientRecord(PatientRecordRequest $request)
    {
        try {

            $data = $request->validated();

            $diagnosis_id = $this->getDiagnosisByName($data['diseases_name']);
            if (!$diagnosis_id) {
                return response()->json(['message' => 'Diagnosis not found or invalid'], 400);
            }
            $prescription_id = $this->getPrescriptionByName($data['medication_name']);
            if (!$prescription_id) {
                return response()->json(['message' => 'Prescription not found or invalid'], 400);
            }
            $data = [
                'doctor_id' => $this->getDoctorIdByName($data['doctor_name']),
                'patient_id' => $this->getPatientIdByName($data['patient_name']),
                'diagnosis_id' => $diagnosis_id['id'],
                'prescription_id' => $prescription_id['id']
            ];

            $missingrecord = [];
            foreach ($data as $key => $value) {
                if (!$value) {
                    $missingrecord = ucfirst(str_replace('_', ' ', $key)) . ' not found or invalid';
                }
            }
            if (!empty($missingrecord)) {
                return response()->json([
                    'message' => 'Validation failed for the following fields:',
                    'error' => $missingrecord . ' not found'
                ], 400);
            }
            $record = PatientRecord::create($data);
            $record = PatientRecord::with(['doctors', 'Patinets', 'diagnosis', 'prescription'])
                ->findOrFail($record->id);
            return response()->json([
                'message' => 'Patient Record Created Successfully',
                'record' => new PatientRecordResource($record),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the patient record',
                'error' => $e->getMessage()
            ], 500);

        }//Done 
    }

    ////////////////////////////////////////////// From DoctorController //////////////////////////////////




    public function getAllPatientRecord($doctor_name)
    {        // from the name i get the ID form the ID get the record 
        $doctor_id = Doctor::where('name', $doctor_name)->first();
        if (!$doctor_id) {
            return response()->json(['message' => 'Doctor not found '], 404);
        }
        $record = $doctor_id->PatientRecords;
        if ($record->isEmpty()) {
            return response()->json(['message' => 'Doctor has no records'], 404);
        }
        return response()->json(['records' => $record], 200);
    }

    //$doctor_id = Doctor::where('name', $doctor_name)->first()->PatientRecords();
// good way but i cant handle the errors


    public function AddPatient(PatientRequest $request)
    {
        $data = $request->validated();

        $patient = Patient::firstOrCreate(
            ['name' => $data['name'], 'phone' => $data['phone']],
            $data
        );

        return response()->json([
            'message' => $patient->wasRecentlyCreated
                ? 'Patient Add successfully'
                : 'Patient already exists',
            'patient' => new PatientResource($patient)
        ], 200);

    }//Done
    public function index()
    {
        //
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
