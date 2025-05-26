<?php

namespace App\Http\Controllers;
use App\Http\Requests\DoctorLogoutRequest;
use App\Http\Requests\VertifyRequest;
use App\Models\Doctor;
use App\Mail\DoctorOtpMail;
use Illuminate\Http\Request;
use App\Http\Requests\DoctorRequest;
use App\Events\DoctorCreate;
use App\Http\Resources\DoctorResource;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;


class DoctorAuthController extends Controller
{
    public function register(DoctorRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $doctor = Doctor::create($data);

        $otp = mt_rand(100000, 999999);

        Cache::put('otp' . $doctor->email, $otp, now()->addMinutes(5));

        Mail::to($data['email'])->send(new DoctorOtpMail($otp));

        event(new DoctorCreate($doctor));

        return response()->json([
            'message' => 'Doctor signin successfully',
            'doctor' => new DoctorResource($doctor),
            'otp' => 'OTP Send to your email',
            'email_verified' => false
        ], 200);
    }//done

    public function verifyOtp(VertifyRequest $request)
    {
        try {
            $data = $request->validated();
            $doctor = Doctor::where('email', $data['email'])->first();

            if (!$doctor) {
                return response()->json([
                    'message' => 'Doctor not found'
                ], 404);
            }

            if ($doctor->email_verified_at) {
                return response()->json(['message' => 'Email is already verified'], 400);
            }

            $cacheKey = 'otp' . $doctor->email;
            $cachedOtp = Cache::get($cacheKey);

            if (!$cachedOtp) {
                return response()->json(['message' => 'OTP expired or not found'], 400);
            }

            if (!hash_equals((string) $cachedOtp, (string) $data['otp'])) {
                return response()->json([
                    'message' => 'Invalid OTP provided'
                ], 400);
            }

            $doctor->email_verified_at = now();
            $doctor->save();

            Cache::forget($cacheKey);

            return response()->json([
                'message' => 'Email verified successfully.',
                'doctor' => new DoctorResource($doctor)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during OTP verification.',
                'error' => $e->getMessage()
            ], 500);

        }
    }


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