<?php

namespace App\Reservation\Domain\Repository;

use App\Reservation\Domain\Reservation;

interface ReservationRepository
{
    public function save(Reservation $reservation): void;

    public function findByIdForUpdate(string $id): ?Reservation;
}
