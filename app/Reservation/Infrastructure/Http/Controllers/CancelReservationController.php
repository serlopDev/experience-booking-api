<?php

namespace App\Reservation\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Reservation\Application\CancelReservation\CancelReservation;
use App\Reservation\Infrastructure\Http\Resources\ReservationResource;
use DateTimeImmutable;

final class CancelReservationController extends Controller
{
    public function __construct(
        private readonly CancelReservation $cancelReservation,
    ) {}

    public function __invoke(string $reservation)
    {
        $cancelledReservation = $this->cancelReservation->execute(
            reservationId: $reservation,
            now: new DateTimeImmutable,
        );

        return ReservationResource::make($cancelledReservation);
    }
}
