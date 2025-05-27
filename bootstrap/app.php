<?php


use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'doctor.email.verified' => App\Http\Middleware\DoctorEmailVerified::class,
            'receptioner.email.verified' => App\Http\Middleware\ReceptionerEmailVerified::class,
            'doctor' => \App\Http\Middleware\DoctorMiddleware::class,
            'receptioner' => \App\Http\Middleware\ReceptionerMiddleware::class,
            'guard' => \App\Http\Middleware\GaurdMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
