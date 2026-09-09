<?php

use App\Experience\Infrastructure\Http\Controllers\CreateExperienceController;
use Illuminate\Support\Facades\Route;

Route::post('/experiences', CreateExperienceController::class)
    ->name('experiences.store');
