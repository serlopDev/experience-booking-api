<?php

namespace App\Session\Infrastructure\Persistence\Eloquent;

use App\Session\Domain\Repository\SessionRepository;
use App\Session\Domain\Session;

final class EloquentSessionRepository implements SessionRepository
{
    public function save(Session $session): void
    {
        SessionModel::query()->updateOrCreate(
            ['id' => $session->id()],
            [
                'experience_id' => $session->experienceId(),
                'starts_at' => $session->startsAt(),
                'max_capacity' => $session->maxCapacity(),
                'reserved_seats' => $session->reservedSeats(),
                'price_in_cents' => $session->priceInCents(),
            ],
        );
    }

    public function existsForExperienceOnDay(string $experienceId, \DateTimeImmutable $day): bool
    {
        return SessionModel::query()
            ->where('experience_id', $experienceId)
            ->whereDate('starts_at', $day->format('Y-m-d'))
            ->exists();
    }

    public function findByIdForUpdate(string $id): ?Session
    {
        $model = SessionModel::query()
            ->whereKey($id)
            ->lockForUpdate()
            ->first();

        if ($model === null) {
            return null;
        }

        return Session::reconstitute(
            id: $model->id,
            experienceId: $model->experience_id,
            startsAt: $model->starts_at->toDateTimeImmutable(),
            maxCapacity: $model->max_capacity,
            reservedSeats: $model->reserved_seats,
            priceInCents: $model->price_in_cents,
        );
    }
}
