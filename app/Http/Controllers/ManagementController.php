<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function SinginDoctor(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'Specialization' => 'required|string',
            'Degree' => 'required|string|in:Bachelor,Master,Doctoral',
            'Available' => 'requied|array',
            'Availabl.*' => 'required|string|string|in:Sunday, Monday, Tuesday, Wednesday, Thursday, Saturday',

        ]);



    }
}
