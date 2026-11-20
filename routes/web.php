<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimetableController;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/timetable', [TimetableController::class, 'index'])
        ->name('timetable.index');

    Route::get('/timetable/create', [TimetableController::class, 'create'])
        ->name('timetable.create');

    Route::post('/timetable', [TimetableController::class, 'store'])
        ->name('timetable.store');

    Route::get('/timetable/{id}/edit', [TimetableController::class, 'edit'])
        ->name('timetable.edit');

    Route::put('/timetable/{id}', [TimetableController::class, 'update'])
        ->name('timetable.update');

    Route::delete('/timetable/{id}', [TimetableController::class, 'destroy'])
        ->name('timetable.destroy');
});
