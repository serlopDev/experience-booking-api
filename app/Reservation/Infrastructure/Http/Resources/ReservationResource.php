<?php

namespace App\Reservation\Infrastructure\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id(),
            'session_id' => $this->sessionId(),
            'user_id' => $this->userId(),
            'contact_email' => $this->contactEmail(),
            'seats' => $this->seats(),
            'total_price_in_cents' => $this->totalPriceInCents(),
            'status' => $this->status()->value,
        ];
    }
}
