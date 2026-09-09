<?php

namespace App\Reservation\Infrastructure\Notification;

use App\Reservation\Application\Notification\ReservationEmailSender;
use App\Reservation\Domain\Reservation;
use Illuminate\Support\Facades\Log;

final class LogReservationEmailSender implements ReservationEmailSender
{
    public function sendReservationCreated(
        Reservation $reservation,
    ): void {
        Log::info('Reservation created email', [
            'reservation_id' => $reservation->id(),
            'email' => $reservation->contactEmail(),
        ]);
    }

    public function sendReservationCancelled(
        Reservation $reservation,
    ): void {
        Log::info('Reservation cancelled email', [
            'reservation_id' => $reservation->id(),
            'email' => $reservation->contactEmail(),
        ]);
    }
}
