<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\LostItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomReportController;
use App\Http\Controllers\TimetableController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/classes', [CatalogController::class, 'index'])->name('classes');
Route::get('/rooms', [CatalogController::class, 'rooms'])->name('rooms');
Route::get('/lost-items', [LostItemController::class, 'index'])->name('lost-items.index');

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

    Route::get('/lost-items/create', [LostItemController::class, 'create'])
        ->name('lost-items.create');

    Route::post('/lost-items', [LostItemController::class, 'store'])
        ->name('lost-items.store');

    Route::get('/lost-items/{id}/edit', [LostItemController::class, 'edit'])
        ->name('lost-items.edit');

    Route::put('/lost-items/{id}', [LostItemController::class, 'update'])
        ->name('lost-items.update');

    Route::patch('/lost-items/{id}/found', [LostItemController::class, 'markFound'])
        ->name('lost-items.found');

    Route::delete('/lost-items/{id}', [LostItemController::class, 'destroy'])
        ->name('lost-items.destroy');

    Route::get('/reports', [RoomReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reports/create', [RoomReportController::class, 'create'])
        ->name('reports.create');

    Route::post('/reports', [RoomReportController::class, 'store'])
        ->name('reports.store')
        ->middleware('throttle:10,1');

    Route::get('/reports/{id}', [RoomReportController::class, 'show'])
        ->whereNumber('id')
        ->name('reports.show');

    Route::get('/reports/{id}/edit', [RoomReportController::class, 'edit'])
        ->whereNumber('id')
        ->name('reports.edit');

    Route::put('/reports/{id}', [RoomReportController::class, 'update'])
        ->whereNumber('id')
        ->name('reports.update');

    Route::delete('/reports/{id}', [RoomReportController::class, 'destroy'])
        ->whereNumber('id')
        ->name('reports.destroy');

    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
});
