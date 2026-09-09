<?php

namespace App\Session\Application\CreateSession;

use App\Experience\Domain\Exception\ExperienceNotFound;
use App\Experience\Domain\Repository\ExperienceRepository;
use App\Session\Domain\Exception\SessionAlreadyExistsForDate;
use App\Session\Domain\Repository\SessionRepository;
use App\Session\Domain\Session;
use DateTimeImmutable;

final readonly class CreateSession
{
    public function __construct(
        private ExperienceRepository $experienceRepository,
        private SessionRepository $sessionRepository,
    ) {}

    public function execute(
        string $id,
        string $experienceId,
        DateTimeImmutable $startsAt,
        int $maxCapacity,
        int $priceInCents,
        DateTimeImmutable $now,
    ): Session {
        if (! $this->experienceRepository->exists($experienceId)) {
            throw new ExperienceNotFound(
                'Experience with id '.$experienceId.' not found.'
            );
        }

        if ($this->sessionRepository->existsForExperienceOnDay(
            $experienceId,
            $startsAt,
        )) {
            throw new SessionAlreadyExistsForDate(
                'A session already exists for this experience on the selected date.'
            );
        }

        $session = Session::create(
            id: $id,
            experienceId: $experienceId,
            startsAt: $startsAt,
            maxCapacity: $maxCapacity,
            priceInCents: $priceInCents,
            now: $now,
        );

        $this->sessionRepository->save($session);

        return $session;
    }
}
