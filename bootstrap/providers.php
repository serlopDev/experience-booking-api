<?php

use App\Providers\AppServiceProvider;
use App\Providers\ExperienceServiceProvider;
use App\Providers\ReservationServiceProvider;
use App\Providers\SessionServiceProvider;
use App\Providers\SharedServiceProvider;

return [
    AppServiceProvider::class,
    ExperienceServiceProvider::class,
    SessionServiceProvider::class,
    ReservationServiceProvider::class,
    SharedServiceProvider::class,
];
