<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginReceptionerRequest;
use App\Http\Requests\RegisterReceptionerRequest;
use App\Http\Requests\VertifyRequest;
use App\Http\Resources\ReceptionerResource;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Receptioner;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

use App\Mail\ReceptionerOtpMail;
class ReceptionAuthController extends Controller
{
    public function register(RegisterReceptionerRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $Receptioner = Receptioner::create($data);

        $otp = mt_rand(100000, 999999);
        Cache::put('otp' . $Receptioner->email, $otp, now()->addMinutes(5));

        Mail::to($data['email'])->send(new ReceptionerOtpMail($otp));

        return response()->json([
            'message' => 'Receptioner signin successfully',
            'Receptioner' => new ReceptionerResource($Receptioner),
            'otp' => 'OTP Send to your email',
            'email_verified' => false
        ], 200);
    }
    public function VerifyOtp(VertifyRequest $request)
    {
        try {

            $data = $request->validated();

            $Receptioner = Receptioner::where('email', $data['email'])->first();

            if (!$Receptioner) {
                return response()->json(['message' => 'Receptioner not found'], 404);
            }

            if ($Receptioner->email_verified_at) {
                return response()->json(['message' => 'Email is already verified'], 400);
            }

            $cacheKey = 'otp' . $Receptioner->email;
            $cacheOtp = Cache::get($cacheKey);

            if (!$cacheOtp) {
                return response()->json(['message' => 'OTP expired or not found'], 400);
            }

            if (!hash_equals((string) $cacheKey, (string) $data['otp'])) {
                return response()->json([
                    'message' => 'Invalid OTP provided'
                ], 400);
            }

            $Receptioner->email_verified_at = now();
            $Receptioner->save();

            Cache::forget($cacheOtp);

            return response()->json([
                'message' => 'Email verified successfully.',
                'doctor' => new ReceptionerResource($Receptioner)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during OTP verification.',
                'error' => $e->getMessage()
            ], 500);

        }
    }



    public function login(LoginReceptionerRequest $request)
    {
        $data = $request->validated();

        $Receptioner = Receptioner::where('email', $data['email'])->first();

        if (!$Receptioner || !Hash::check($data['password'], $Receptioner->password)) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        $token = $Receptioner->createToken('Receptioner-token')->plainTextToken;

        return response()->json([
            'message' => 'Receptioner login Successfully',
            'user' => new AuthResource($Receptioner),
            'token' => $token
        ], 200);
    }

}

