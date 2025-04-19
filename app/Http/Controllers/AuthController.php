<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Hash;
class AuthController extends Controller
{
    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:225',
            'email' => 'required|email|string',
            'password' => 'required|string|min:7|confirmed'
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)//dont forget to hashing the password
        ]);

        return response()->json($user);

    }
}
