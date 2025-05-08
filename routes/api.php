<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\WaitingListController;
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
Route::get('doctor/appointment/daily', [DoctorController::class, 'getDailyAppointment']);
Route::get('doctor/appointment/daily-list/', [WaitingListController::class, 'GetDoctorWaitingDailylist']);
Route::get('doctor/appointment/queue/', [WaitingListController::class, 'getDoctorQueue']);

//////////////////////////////////////////////Reception  
Route::post('reception/patient', [ReceptionController::class, 'AddPatient']);
Route::post('reception/patient/record', [ReceptionController::class, 'CreatePatientRecord']);
Route::get('reception/doctor-record/{name}', [ReceptionController::class, 'getAllPatientRecord']);
Route::get('reception/splz/{Specialization}', [AppointmentController::class, 'getDoctorBySplz']);
Route::post('reception/appointment/booking', [AppointmentController::class, 'CreateAppointment']);
Route::put('reception/appointment/update', [AppointmentController::class, 'UpdateAppointment']);
Route::get('reception/appointment/daily', [AppointmentController::class, 'getDailyAppointment']);
Route::delete('reception/appointment/cancel/{id}', [AppointmentController::class, 'CancelAppointment']);
Route::get('reception/appointment/free-time', [AppointmentController::class, 'getAvailableTimes']);
Route::get('reception/appointment/available-day', [AppointmentController::class, 'getDoctorAvailableDay']);
Route::get('reception/appointment/list/{doctorname}', [WaitingListController::class, 'GetDoctorWaitinglist']);
Route::get('reception/appointment/daily-list/', [WaitingListController::class, 'GetDoctorWaitingDailylist']);
Route::get('reception/appointment/queue/', [AppointmentController::class, 'getDoctorQueue']);
Route::delete('reception/appointment/cancel-next/', [WaitingListController::class, 'DeleteNextAppointment']);




//////////////////////////////////////////////Management
Route::post('management/doctor', [ManagementController::class, 'DoctorSignin']);