<?php

namespace App\Http\Controllers;
use App\Events\AppointmentCreated;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\AvailableAppointmentRequest;
use App\Http\Requests\DailyAppointmentRequest;
use App\Http\Requests\DiagnosisRequest;
use App\Http\Requests\DoctorAvalibleDayRequest;
use App\Http\Requests\DoctorQueueRequest;
use App\Notifications\AppointmentReminder;
use App\Http\Requests\PrescriptionRequest;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Resources\DoctorAvalibleDayResource;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Requests\CreateAppointmentRequest;
use App\Http\Resources\DailyAppintmentResource;
use App\Http\Resources\PatientRecordResource;
use App\Http\Resources\PrescriptionResource;
use App\Http\Requests\PatientRecordRequest;
use App\Http\Resources\DiagnosisResource;
use App\Http\Resources\PatientResource;
use App\Http\Resources\DoctorResource;
use PhpParser\Node\Stmt\TryCatch;
use App\Models\DoctorSchedule;
use App\Models\PatientRecord;
use App\Models\prescription;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\QueueEntry;
use App\Models\Diagnosis;
use App\Models\Patient;
use App\Models\Doctor;
use Carbon\Carbon;
class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('receptioner');
    }

    private function getDoctorIdByName($doctor_name)
    {
        return Doctor::where('name', $doctor_name)->value('id');
    }

    private function getPatientIdByName($patient_name)
    {
        return Patient::where('name', $patient_name)->value('id');
    }

    private function getDoctorBySplz(Request $request)
    {
        $doctors = Doctor::when($request->Specialization, function ($query, $Specialization) {
            return $query->where('Specialization', $Specialization);
        })->get();
        return response()->json(['message' => $doctors->isEmpty() ? 'There is no Doctor with this Specialization' : $doctors]);
    }


    public function CreateAppointment(CreateAppointmentRequest $request)
    {
        try {
            $data = $request->validated();

            $patient = Patient::where('id', $this->getPatientIdByName($data['patient_name']))->first();
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
                    $missinginfo[] = ucfirst(str_replace('_', ' ', $key)) . ' not found or invalid';
                }
            }
            if (!empty($missinginfo)) {
                return response()->json([
                    'message' => 'Validation failed for the following fields:',
                    'error' => implode(', ', $missinginfo)
                ], 400);
            }

            $appointment = Appointment::create($data);
            Notification::send($patient, new AppointmentReminder($data));
            event(new AppointmentCreated($appointment));
            return response()->json(['message' => $appointment], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating appointment',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function UpdateAppointment(UpdateAppointmentRequest $request)
    {
        $data = $request->validated();
        $doctorId = $this->getDoctorIdByName($data['doctor_name']);// if put this in $data it will crush
        $patientId = $this->getPatientIdByName($data['patient_name']);//

        $appointment = Appointment::where('doctor_id', $doctorId)->where('patient_id', $patientId)->where('appointment_date', $request->input('appointment_date'))->first();

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
        $appointment->update($data);

        return response()->json($appointment, 200);
    }
    public function getAvailableTimes(AvailableAppointmentRequest $request)
    {
        $data = $request->validated();
        $checkingDocotr = $this->getDoctorIdByName($data['doctor_name']);
        if (!$checkingDocotr) {
            return response()->json(['message' => 'there is no doctor like ' . $data['doctor_name']], 404);
        }
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

        $blockedTimess = array_unique($blockedTimes);// remove the duplicate
        $availableSlots = array_values(array_diff($allTimeSlots, $blockedTimes));
        // array_vaules : reindex the array from 0
        return response()->json([$data['appointment_date'] => $availableSlots]);
    }

    public function getDailyAppointment(DailyAppointmentRequest $request)
    {
        $data = $request->validated();
        $dailyAppointment = Appointment::where('doctor_id', $this->getDoctorIdByName($data['doctor_name']))
            ->where('appointment_date', $data['appointment_date'])
            ->with('patient')->orderBy('appointment_time')->get();

        return response()->json(['message' => 'there is time to slot'], 404);
        if ($dailyAppointment->isEmpty()) {
        }

        return DailyAppintmentResource::collection($dailyAppointment);
    }

    public function getDoctorAvailableDay(DoctorAvalibleDayRequest $request)
    {
        $data = $request->validated();
        $avilableDay = DoctorSchedule::where('doctor_id', $this->getDoctorIdByName($data['doctor_name']))->get();
        return response()->json(DoctorAvalibleDayResource::collection($avilableDay));
    }



    public function CancelAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return response()->json(['message' => 'Appointment canceled']);
    }
}



