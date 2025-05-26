<?php

namespace App\Http\Controllers;

use App\Events\DoctorCreate;
use App\Http\Requests\DoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function DoctorSignin(DoctorRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $doctor = Doctor::create($data);
        event(new DoctorCreate($doctor));
        event(new Registered($doctor));

        return response()->json(['message' => 'Doctor signin successfully', 'doctor' => new DoctorResource($doctor)], 200);


    }
}
