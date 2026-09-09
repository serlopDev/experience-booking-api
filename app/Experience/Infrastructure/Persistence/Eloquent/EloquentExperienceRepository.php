<?php

namespace App\Experience\Infrastructure\Persistence\Eloquent;

use App\Experience\Domain\Experience;
use App\Experience\Domain\Repository\ExperienceRepository;

final class EloquentExperienceRepository implements ExperienceRepository
{
    public function save(Experience $experience): void
    {
        ExperienceModel::query()->create(
            [
                'id' => $experience->id(),
                'provider_id' => $experience->providerId(),
                'title' => $experience->title(),
                'description' => $experience->description(),
            ]
        );
    }

    public function exists(string $id): bool
    {
        return ExperienceModel::query()
            ->whereKey($id)
            ->exists();
    }
}
