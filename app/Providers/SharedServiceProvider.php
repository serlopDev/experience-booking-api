<?php

namespace App\Providers;

use App\Shared\Application\TransactionManager;
use App\Shared\Infrastructure\Persistence\LaravelTransactionManager;
use Illuminate\Support\ServiceProvider;

final class SharedServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TransactionManager::class,
            LaravelTransactionManager::class,
        );
    }
}
