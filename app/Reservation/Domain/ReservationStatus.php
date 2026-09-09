<?php

namespace App\Reservation\Domain;

enum ReservationStatus: string
{
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
}
