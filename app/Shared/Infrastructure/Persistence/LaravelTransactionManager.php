<?php

namespace App\Shared\Infrastructure\Persistence;

use App\Shared\Application\TransactionManager;
use Illuminate\Support\Facades\DB;

final class LaravelTransactionManager implements TransactionManager
{
    public function run(callable $operation): mixed
    {
        return DB::transaction($operation);
    }
}
