<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\ManagementController;
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
Route::get('doctor/{id}/all-record', [DoctorController::class, 'getAllPatientRecord']);
Route::get('doctor/{id}/record', [DoctorController::class, 'getPatientRecord']);
Route::post('doctor/diagnosis', [DoctorController::class, 'Diagnosis']);
Route::post('doctor/prescription', [DoctorController::class, 'Prescription']);
Route::post('doctor/patient/record', [DoctorController::class, 'CreatePatientRecord']);

Route::get('doctor/Find/{doctor_name}', [DoctorController::class, 'FindDoctorbyname']);

//////////////////////////////////////////////Reception
Route::post('reception/patient', [ReceptionController::class, 'AddPatient']);
Route::post('reception/patient/record', [ReceptionController::class, 'CreatePatientRecord']);

//////////////////////////////////////////////Management
Route::post('management/doctor', [ManagementController::class, 'DoctorSignin']);