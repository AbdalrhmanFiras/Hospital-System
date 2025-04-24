<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\ManagementController;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////Doctor  
Route::get('doctor/{name}/record', [DoctorController::class, 'getPatientRecord']);
Route::post('doctor/diagnosis', [DoctorController::class, 'Diagnosis']);
Route::post('doctor/prescription', [DoctorController::class, 'Prescription']);
Route::post('doctor/patient/record', [DoctorController::class, 'CreatePatientRecord']);

//////////////////////////////////////////////Reception
Route::post('reception/patient', [ReceptionController::class, 'AddPatient']);
Route::post('reception/patient/record', [ReceptionController::class, 'CreatePatientRecord']);
Route::get('reception/doctor-record/{name}', [ReceptionController::class, 'getAllPatientRecord']);
Route::get('reception/splz/{Specialization}', [AppointmentController::class, 'getDoctorBySplz']);
Route::post('reception/appointment/booking', [AppointmentController::class, 'CreateAppointment']);
Route::get('reception/appointment/daily', [AppointmentController::class, 'getDailyAppointment']);




//////////////////////////////////////////////Management
Route::post('management/doctor', [ManagementController::class, 'DoctorSignin']);