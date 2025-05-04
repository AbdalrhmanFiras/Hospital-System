<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\Doctor;
class WaitingListController extends Controller
{
    public function getDoctorIdByName($doctor_name)
    {
        return Doctor::where('name', $doctor_name)->value('id');
    }

    public function GetDoctorWaitinglist($doctorname)
    {
        $list = Appointment::where('doctor_id', $this->getDoctorIdByName($doctorname))
            ->where('status', 'pending')->orderBy('created_at')
            ->with('patient:id,name')->get(['id', 'appointment_time', 'patient_id', 'appointment_date']);

        return response()->json($list);

    }



}
