<?php

namespace App\Reservation\Application\Notification;

use App\Reservation\Domain\Reservation;

interface ReservationEmailSender
{
    public function sendReservationCreated(
        Reservation $reservation,
    ): void;

    public function sendReservationCancelled(
        Reservation $reservation,
    ): void;
}
