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
}
