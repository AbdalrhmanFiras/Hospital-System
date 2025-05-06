<?php

namespace App\Http\Controllers;
use App\Http\Requests\DailyAppointmentRequest;
use App\Http\Resources\DailyAppintmentResource;
use App\Http\Requests\DiagnosisRequest;
use App\Http\Requests\PrescriptionRequest;
use App\Http\Requests\PatientRecordRequest;
use App\Http\Resources\DiagnosisResource;
use App\Http\Resources\PatientResource;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\PatientRecordResource;
use App\Http\Resources\PrescriptionResource;
use App\Models\Diagnosis;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\PatientRecord;
use App\Models\prescription;
use Illuminate\Http\Request;

class DoctorController extends Controller
{

    public function getAppointmentStatus($doctor_name, $patient_name)
    {

        $appointment = Appointment::where('doctor_id', $this->getDoctorIdByName($doctor_name))->where(
            'patient_id',
            $this->getPatientIdByName($patient_name)
        )->first();

        return $appointment;
    }

    public function getDoctorIdByName($doctor_name)
    {
        return Doctor::where('name', $doctor_name)->value('id');
    }

    public function getPatientIdByName($patient_name)
    {
        return Patient::where('name', $patient_name)->value('id');
    }

    public function getDiagnosisByName($diseases_name)
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
    public function getPrescriptionByName($medication_name)
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

            $app_status = $this->getAppointmentStatus(
                $request->input('doctor_name'),
                $request->input('patient_name')
            );
            $app_status->status = Appointment::STATUS_COMPLETED;
            $app_status->save();
            return response()->json([
                'message' => 'Patient Record Created Successfully',
                'status' => $app_status->status,
                'record' => new PatientRecordResource($record),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the patient record',
                'error' => $e->getMessage()
            ], 500);

        }//Done 
    }

    public function getPatientRecord($patient_name)
    {
        $patient = Patient::where('name', $patient_name)->first();
        if (!$patient) {
            return response()->json(['message' => 'patient not found'], 404);
        }
        $records = $patient->PatientRecords;
        if ($records->isEmpty()) {
            return response()->json(['message' => 'patient has no patient records'], 404);
        }
        return response()->json(['records' => $records], 200);
    }//Done

    public function Diagnosis(DiagnosisRequest $request)
    {
        $data = $request->validated();
        $diagnosisdata = [
            'diseases_name' => $data['diseases_name'],
            'diseases' => $data['diseases'],
            'diagnoses' => $data['diagnoses'],
            'allergies' => $allergies = $data['allergies'] ?? null,
            'doctor_id' => $this->getDoctorIdByName($data['doctor_name']),
            'patient_id' => $this->getPatientIdByName($data['patient_name']),
        ];

        $missingdata = [];
        foreach ($diagnosisdata as $key => $value) {
            if (!$value && $key !== 'allergies') {
                $missingdata = ucfirst(str_replace('_', ' ', $key));
            }
        }
        if (!empty($missingdata)) {
            return response()->json([
                'message' => 'Validation failed for the following fields:',
                'error' => $missingdata . ' not found'
            ], 400);
        }

        $diagnosis = Diagnosis::create($diagnosisdata);

        return response()->json(['message' => 'Diagnosis Added Successfully', 'diagnosis' => new DiagnosisResource($diagnosis)], 200);
    }//Done

    public function Prescription(PrescriptionRequest $request)
    {

        $data = $request->validated();

        $diagnosis_id = $this->getDiagnosisByName($data['diagnosis_name']);
        if (!$diagnosis_id) {
            return response()->json(['message' => 'Diagnosis not found or invalid'], 400);
        }

        $prescriptiondata = [
            'medication_name' => $data['medication_name'],
            'dosage_amount' => $data['dosage_amount'] ?? null,
            'frequency' => $data['frequency'] ?? null,
            'duration' => $data['duration'],
            'diagnosis_id' => $diagnosis_id['id'],
            'doctor_id' => $this->getDoctorIdByName($data['doctor_name']),
            'patient_id' => $this->getPatientIdByName($data['patient_name']),
        ];

        $missingdata = [];

        foreach ($missingdata as $key => $value) {
            if (!$value && $key !== 'frequency' && $key !== 'dosage_amount') {
                $missingdata = ucfirst(str_replace('_', ' ', $key));
            }
        }

        if (!empty($missingdata)) {
            return response()->json([
                'message' => 'Validation failed for the following fields:',
                'error' => $missingdata . 'not found'
            ]);
        }
        $prescription = Prescription::create($prescriptiondata);

        $app_status = $this->getAppointmentStatus($request->input('doctor_name'), $request->input('patient_name'));
        $app_status->status = Appointment::STATUS_PROCESS;
        $app_status->save();
        return response()->json([
            'message' => 'Prescription Added Successfully',
            'status' => $app_status->status,
            'prescription' => new PrescriptionResource($prescription->load('diagnosis')),
        ], 200);
    }//Doneß

    public function getDailyAppointment(DailyAppointmentRequest $request)
    {
        $data = $request->validated();
        $dailyAppointment = Appointment::where('doctor_id', $this->getDoctorIdByName($data['doctor_name']))
            ->where('appointment_date', $data['appointment_date'])
            ->with('patient')->orderBy('appointment_time')->get();

        if ($dailyAppointment->isEmpty()) {
            return response()->json(['message' => 'there is time to slot'], 404);
        }

        return DailyAppintmentResource::collection($dailyAppointment);
    }

}
