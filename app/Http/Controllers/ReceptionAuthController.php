<?php

namespace App\Http\Controllers;

use Cache;
use Hash;
use Illuminate\Http\Request;
use App\Models\Receptioner;
use Mail;
use App\Mail\ReceptionerOtpMail;
class ReceptionAuthController extends Controller
{
    public function register(ReceptionAuthController $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $Receptioner = Receptioner::create($data);

        $otp = mt_rand(1000000, 999999);

        Cache::put('otp' . $Receptioner->email, $otp, now()->addMinutes(5));

        Mail::to($data['email'])->send(new ReceptionerOtpMail($otp));



        return response()->json([
            'message' => 'Doctor signin successfully',
            'doctor' => new ($Receptioner),
            'otp' => 'OTP Send to your email',
            'email_verified' => false
        ], 200);


    }
}
