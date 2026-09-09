<?php

namespace App\Providers;

use App\Experience\Domain\Repository\ExperienceRepository;
use App\Experience\Infrastructure\Persistence\Eloquent\EloquentExperienceRepository;
use Illuminate\Support\ServiceProvider;

final class ExperienceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ExperienceRepository::class,
            EloquentExperienceRepository::class,
        );
    }
}
