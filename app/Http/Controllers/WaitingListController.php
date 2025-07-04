<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\DailyWaitingListRequset;
use App\Models\Appointment;
use App\Models\AppointmentHistory;
use App\Models\PatientRecord;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\QueueEntry;
use App\Http\Requests\DoctorQueueRequest;
class WaitingListController extends Controller
{

    public function __construct()
    {
        $this->middleware('receptioner')->except('GetDoctorWaitingDailylist', 'getDoctorQueue', );
    }

    private function getPatientIdByName($patient_name)
    {
        return Patient::where('name', $patient_name)->value('id');
    }

    private function getDoctorIdByName($doctor_name)
    {
        return Doctor::where('name', $doctor_name)->value('id');
    }



    public function getDoctorQueue(DoctorQueueRequest $request)
    {
        $doctor_id = $this->getDoctorIdByName($doctor_name = $request->input('doctor_name'));
        if (!$doctor_id) {
            return response()->json(['message' => 'there is no Doctor name like ' . $doctor_name], 404);
        }
        $queue = QueueEntry::where('queue_entries.doctor_id', $doctor_id)
            ->join('appointments', 'queue_entries.appointment_id', '=', 'appointments.id')
            ->orderBy('appointments.appointment_time')
            ->select('queue_entries.*')
            ->get();
        return response()->json($queue);
    }

    public function GetDoctorWaitinglist($doctorname)// all
    {
        $list = Appointment::where('doctor_id', $this->getDoctorIdByName($doctorname))
            ->where('status', 'pending')->orderBy('created_at')
            ->with('patient:id,name')->get(['id', 'appointment_time', 'patient_id', 'appointment_date']);

        return response()->json($list);

    }

    public function GetDoctorWaitingDailylist(DailyWaitingListRequset $request)
    {
        $data = $request->validated();

        $list = Appointment::where('doctor_id', $this->getDoctorIdByName($data['doctor_name']))->where('appointment_date', $data['appointment_date'])
            ->where('status', 'pending')->orderBy('created_at')
            ->with('patient:id,name')->get(['id', 'appointment_time', 'patient_id']);

        return response()->json($list);

    }

}




