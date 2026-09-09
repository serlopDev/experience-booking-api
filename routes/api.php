<?php

use App\Experience\Infrastructure\Http\Controllers\CreateExperienceController;
use App\Reservation\Infrastructure\Http\Controllers\CreateReservationController;
use App\Session\Infrastructure\Http\Controllers\CreateSessionController;
use Illuminate\Support\Facades\Route;

Route::post('/experiences', CreateExperienceController::class)
    ->name('experiences.store');

Route::post(
    '/experiences/{experience}/sessions',
    CreateSessionController::class,
)->name('experiences.sessions.store');

Route::post(
    'sessions/{session}/reservations',
    CreateReservationController::class,
)->name('sessions.reservations.store');
