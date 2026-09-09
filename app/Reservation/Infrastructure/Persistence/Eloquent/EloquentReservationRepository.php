<?php

namespace App\Reservation\Infrastructure\Persistence\Eloquent;

use App\Reservation\Domain\Repository\ReservationRepository;
use App\Reservation\Domain\Reservation;

final class EloquentReservationRepository implements ReservationRepository
{
    public function save(Reservation $reservation): void
    {
        ReservationModel::query()->updateOrCreate(
            ['id' => $reservation->id()],
            [
                'session_id' => $reservation->sessionId(),
                'user_id' => $reservation->userId(),
                'contact_email' => $reservation->contactEmail(),
                'seats' => $reservation->seats(),
                'total_price_in_cents' => $reservation->totalPriceInCents(),
                'status' => $reservation->status()->value,
            ],
        );
    }
}
