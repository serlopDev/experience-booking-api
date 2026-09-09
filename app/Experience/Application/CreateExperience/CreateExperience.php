<?php

namespace App\Experience\Application\CreateExperience;

use App\Experience\Domain\Experience;
use App\Experience\Domain\Repository\ExperienceRepository;

final readonly class CreateExperience
{
    public function __construct(
        private ExperienceRepository $repository,
    ) {}

    public function execute(
        string $id,
        string $providerId,
        string $title,
        string $description,
    ): Experience {
        $experience = Experience::create(
            $id,
            $providerId,
            $title,
            $description,
        );

        $this->repository->save($experience);

        return $experience;
    }
}
