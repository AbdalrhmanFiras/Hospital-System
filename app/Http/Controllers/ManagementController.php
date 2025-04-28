<?php

namespace App\Http\Controllers;

use App\Events\DoctorCreate;
use App\Http\Requests\DoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function DoctorSignin(DoctorRequest $request)
    {
        $data = $request->validated();
        $doctor = Doctor::create($data);
        event(new DoctorCreate($doctor));
        return response()->json(['message' => 'Doctor signin successfully', 'doctor' => new DoctorResource($doctor)], 200);


    }
}
