<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorAuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\WaitingListController;
use App\Http\Controllers\ReceptionAuthController;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

///////////////////////////////////////////////////////Doctor Auth///////////////////////////////////////////////////////////////////////////////
Route::post('/register', [DoctorAuthController::class, 'register']);
Route::post('/login', [DoctorAuthController::class, 'login'])->middleware('doctor.email.verified');
Route::post('/logout', [DoctorAuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/verify-otp', [DoctorAuthController::class, 'verifyOtp']);


//////////////////////////////////////////////Doctor  
Route::middleware('doctor')->prefix('doctor')->group(function () {
    Route::get('{name}/record', [DoctorController::class, 'getPatientRecord']);
    Route::post('diagnosis', [DoctorController::class, 'Diagnosis']);
    Route::post('prescription', [DoctorController::class, 'Prescription']);
    Route::post('patient/record', [DoctorController::class, 'CreatePatientRecord']);
    Route::get('appointment/daily', [DoctorController::class, 'getDailyAppointment']);
    Route::delete('appointment/cancel-next/', [DoctorController::class, 'DeleteNextAppointment']);


    Route::get('appointment/daily-list/', [WaitingListController::class, 'GetDoctorWaitingDailylist']);
    Route::get('appointment/queue/', [WaitingListController::class, 'getDoctorQueue']);
});


///////////////////////////////////////////////////////Receptioner Auth///////////////////////////////////////////////////////////////////////////////
Route::post('/reception-register', [ReceptionAuthController::class, 'register']);
Route::post('/reception-login', [ReceptionAuthController::class, 'login'])->middleware('receptioner.email.verified');
Route::post('/reception-logout', [ReceptionAuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/reception-verify-otp', [ReceptionAuthController::class, 'verifyOtp']);


//////////////////////////////////////////////Reception 
Route::prefix('reception')->group(function () {

    Route::post('patient', [ReceptionController::class, 'AddPatient']);
    Route::post('patient/record', [ReceptionController::class, 'CreatePatientRecord']);
    Route::get('doctor-record/{name}', [ReceptionController::class, 'getAllPatientRecord']);

    Route::get('splz/{Specialization}', [AppointmentController::class, 'getDoctorBySplz']);
    Route::post('appointment/booking', [AppointmentController::class, 'CreateAppointment']);
    Route::put('appointment/update', [AppointmentController::class, 'UpdateAppointment']);
    Route::get('appointment/daily', [AppointmentController::class, 'getDailyAppointment']);
    Route::delete('appointment/cancel/{id}', [AppointmentController::class, 'CancelAppointment']);
    Route::get('appointment/free-time', [AppointmentController::class, 'getAvailableTimes']);
    Route::get('appointment/available-day', [AppointmentController::class, 'getDoctorAvailableDay']);

    Route::get('appointment/list/{doctorname}', [WaitingListController::class, 'GetDoctorWaitinglist']);
    Route::get('appointment/daily-list/', [WaitingListController::class, 'GetDoctorWaitingDailylist']);
    Route::get('appointment/queue/', [WaitingListController::class, 'getDoctorQueue']);

});


//////////////////////////////////////////////Management
Route::post('management/doctor', [ManagementController::class, 'DoctorSignin']);