<?php

namespace App\Http\Controllers;
use App\Http\Requests\AvailableAppointmentRequest;
use App\Http\Requests\DailyAppointmentRequest;
use App\Http\Requests\DiagnosisRequest;
use App\Http\Requests\PrescriptionRequest;
use App\Http\Requests\PatientRecordRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\DiagnosisResource;
use App\Http\Resources\PatientResource;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\DailyAppintmentResource;
use App\Http\Resources\PatientRecordResource;
use App\Http\Resources\PrescriptionResource;
use App\Models\Diagnosis;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientRecord;
use App\Models\prescription;
use App\Http\Requests\CreateAppointmentRequest;
use App\Models\Appointment;
use Carbon\Carbon;
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


        $appointmentTime = Carbon::createFromFormat('H:i', $data['appointment_time']);
        $start = $appointmentTime->copy()->subMinutes(30)->format('H:i');
        $end = $appointmentTime->copy()->addMinutes(30)->format('H:i');
        //false or true
        $isAvailable = !Appointment::where('doctor_id', $this->getDoctorIdByName($data['doctor_name']))
            ->where('appointment_date', $data['appointment_date'])
            ->whereBetween('appointment_time', [$start, $end])->exists();
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
    public function UpdateAppointment(UpdateAppointmentRequest $request, $id)
    {
        $data = $request->validated();
        $appointment = Appointment::findOrFail($id);


        $doctorId = $this->getDoctorIdByName($data['doctor_name']);// if put this in $data it will crush
        $patientId = $this->getPatientIdByName($data['patient_name']);//

        $data = [
            'doctor_id' => $doctorId,
            'patient_id' => $patientId,
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
        ];

        $time = Carbon::createFromFormat('H:i', $data['appointment_time']);
        $startWindow = $time->copy()->subMinutes(30)->format('H:i');
        $endWindow = $time->copy()->addMinutes(30)->format('H:i');

        $conflictExists = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $data['appointment_date'])
            ->whereBetween('appointment_time', [$startWindow, $endWindow])
            ->exists();

        if ($conflictExists) {
            return response()->json(['message' => 'Time slot not available within 30-minute buffer'], 409);
        }

        // No conflict: update the appointment
        $appointment->update($data);

        return response()->json($appointment, 200);
    }
    public function A(AvailableAppointmentRequest $request)
    {
        $data = $request->validated();

        $existingAppointments = Appointment::where('doctor_id', $this->getDoctorIdByName($data['doctor_name']))
            ->where('appointment_date', $data['appointment_date'])
            ->pluck('appointment_time');

        $allTimeSlots = [];
        $startTime = now()->startOfDay()->setTime(8, 0);
        $endTime = now()->startOfDay()->setTime(17, 0);
        $intervalMinutes = (int) $data['interval_minutes'] ?? 30;

        //by now() function i get the current date and time 
        //by startofDay() function i restart the time 
        //by setTIme() function i set start time

        $currentTime = $startTime->copy();
        while ($currentTime <= $endTime) {// its end when the $currentTime is equal or bigger
            $allTimeSlots[] = $currentTime->format('H:i');//but currentTime but format to look like 00:00
            $currentTime->addMinutes($intervalMinutes);// increase the currentTime by the min i set 
        }

        $blockedTimes = [];
        foreach ($existingAppointments as $time) {
            $time = Carbon::createFromFormat('H:i:s', $time);

            for ($i = -30; $i <= 30; $i += $intervalMinutes) {
                $blockedTimes[] = $time->copy()->addMinutes($i)->format('H:i');
            }
        }

        $blockedTimess = array_unique($blockedTimes);
        $availableSlots = array_values(array_diff($allTimeSlots, $blockedTimes));

        return response()->json([$data['appointment_date'] => $availableSlots]);
    }

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

    public function CancelAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return response()->json(['message' => 'Appointment canceled']);

    }
}


