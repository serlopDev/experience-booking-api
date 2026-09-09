<?php

namespace App\Providers;

use App\Reservation\Domain\Repository\ReservationRepository;
use App\Reservation\Infrastructure\Persistence\Eloquent\EloquentReservationRepository;
use Illuminate\Support\ServiceProvider;

final class ReservationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ReservationRepository::class,
            EloquentReservationRepository::class,
        );
    }
}
