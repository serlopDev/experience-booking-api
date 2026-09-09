<?php

namespace App\Reservation\Infrastructure\Persistence\Eloquent;

use App\Reservation\Domain\Repository\ReservationRepository;
use App\Reservation\Domain\Reservation;
use App\Reservation\Domain\ReservationStatus;

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

    public function findByIdForUpdate(string $id): ?Reservation
    {
        $model = ReservationModel::query()
            ->whereKey($id)
            ->lockForUpdate()
            ->first();

        if ($model === null) {
            return null;
        }

        return Reservation::reconstitute(
            id: $model->id,
            sessionId: $model->session_id,
            userId: $model->user_id,
            contactEmail: $model->contact_email,
            seats: $model->seats,
            totalPriceInCents: $model->total_price_in_cents,
            status: ReservationStatus::from($model->status),
        );
    }
}
