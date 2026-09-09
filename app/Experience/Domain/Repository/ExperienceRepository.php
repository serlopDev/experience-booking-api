<?php

namespace App\Experience\Domain\Repository;

use App\Experience\Domain\Experience;

interface ExperienceRepository
{
    public function save(Experience $experience): void;

    public function exists(string $id): bool;
}
