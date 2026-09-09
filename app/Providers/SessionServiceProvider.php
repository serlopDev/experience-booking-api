<?php

namespace App\Providers;

use App\Session\Domain\Repository\SessionRepository;
use App\Session\Infrastructure\Persistence\Eloquent\EloquentSessionRepository;
use Illuminate\Support\ServiceProvider;

final class SessionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            SessionRepository::class,
            EloquentSessionRepository::class,
        );
    }
}
