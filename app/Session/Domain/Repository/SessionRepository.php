<?php

namespace App\Session\Domain\Repository;

use App\Session\Domain\Session;
use DateTimeImmutable;

interface SessionRepository
{
    public function save(Session $session): void;

    public function existsForExperienceOnDay(
        string $experienceId,
        DateTimeImmutable $date,
    ): bool;

    public function findByIdForUpdate(string $id): ?Session;
}
