<?php

namespace App\Http\Controllers;
use App\Http\Requests\DoctorLogoutRequest;
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Http\Requests\DoctorRequest;
use App\Events\DoctorCreate;
use App\Http\Resources\DoctorResource;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AuthResource;


class DoctorAuthController extends Controller
{


    public function register(DoctorRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $doctor = Doctor::create($data);

        event(new DoctorCreate($doctor));//Schudel
        event(new Registered($doctor));//verification


        return response()->json([
            'message' => 'Doctor signin successfully',
            'doctor' => new DoctorResource($doctor),
            'email_verified' => false
        ], 200);
    }//done


    public function login(LoginRequest $request)
    {
        $data = $request->validated();


        if (!Auth::guard('doctor')->attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return response()->json(['message' => 'invaild email or password'], 401);
        }

        $doctor = Doctor::where('email', $data['email'])->firstOrFail();
        $token = $doctor->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'User login Successfully',
            'user' => new AuthResource($doctor),
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {

        if ($request->user()) {

            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'The doctor logged out successfully']);
        }

        return response()->json(['message' => 'No authenticated doctor to logout.'], 401);
    }


}

