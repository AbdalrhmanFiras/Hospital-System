<?php

namespace App\Http\Controllers;
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
use App\Models\Patient;
use App\Models\PatientRecord;
use App\Models\prescription;
use App\Http\Requests\CreateAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class AppointmentController extends Controller
{

    public function getDoctorIdByName($doctor_name)
    {
        return Doctor::where('name', $doctor_name)->value('id');
    }

    public function getPatientIdByName($patient_name)
    {
        return Patient::where('name', $patient_name)->value('id');
    }

    public function getDoctorBySplz(Request $request)
    {
        $doctors = Doctor::when($request->Specialization, function ($query, $Specialization) {
            return $query->where('Specialization', $Specialization);
        })->get();
        return response()->json(['message' => $doctors->isEmpty() ? 'There is no Doctor with this Specialization' : $doctors]);
    }


    public function CreateAppointment(CreateAppointmentRequest $request)
    {
        $data = $request->validated();

        //false or true
        $isAvailable = !Appointment::where('doctor_id', $this->getDoctorIdByName($data['doctor_name']))
            ->where('appointment_date', $data['appointment_date'])
            ->where('appointment_time', $data['appointment_time'])->exists();
        //true or false
        if (!$isAvailable) {
            return response()->json(['message' => 'Time slot not available'], 409);
        }

        $data = [
            'doctor_id' => $this->getDoctorIdByName($data['doctor_name']),
            'patient_id' => $this->getPatientIdByName($data['patient_name']),
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
        ];

        $missinginfo = [];
        foreach ($data as $key => $value) {
            if (!$value) {
                $missinginfo = ucfirst(str_replace('_', ' ', $key)) . ' not found or invalid';
            }

            if (!empty($missinginfo)) {
                return response()->json([
                    'message' => 'Validation failed for the following fields:',
                    'error' => $missinginfo . ' not found'
                ], 400);
                ;
            }
        }
        $appointment = Appointment::create($data);

        return response()->json(['message' => $appointment], 200);
    }
}
