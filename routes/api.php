<?php

use App\Experience\Infrastructure\Http\Controllers\CreateExperienceController;
use App\Session\Infrastructure\Http\Controllers\CreateSessionController;
use Illuminate\Support\Facades\Route;

Route::post('/experiences', CreateExperienceController::class)
    ->name('experiences.store');

Route::post(
    '/experiences/{experience}/sessions',
    CreateSessionController::class,
)->name('experiences.sessions.store');
