<?php

namespace App\Shared\Application;

interface TransactionManager
{
    public function run(callable $operation): mixed;
}
