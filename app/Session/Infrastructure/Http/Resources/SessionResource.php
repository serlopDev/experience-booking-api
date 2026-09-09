<?php

namespace App\Session\Infrastructure\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class SessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id(),
            'experience_id' => $this->experienceId(),
            'starts_at' => $this->startsAt()->format(DATE_ATOM),
            'max_capacity' => $this->maxCapacity(),
            'reserved_seats' => $this->reservedSeats(),
            'available_seats' => $this->availableSeats(),
            'price_in_cents' => $this->priceInCents(),
        ];
    }
}
