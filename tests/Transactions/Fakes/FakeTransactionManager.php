<?php

namespace LuKun\Workflow\Tests\Transactions\Fakes;

use LuKun\Workflow\Transactions\ITransactionManager;

class FakeTransactionManager implements ITransactionManager
{
    /** @var callable */
    public $onBeginTransaction;
    /** @var callable */
    public $onCommitTransaction;
    /** @var callable */
    public $onRollbackTransaction;

    public function __construct()
    {
        $this->onBeginTransaction = function () { };
        $this->onCommitTransaction = function () { };
        $this->onRollbackTransaction = function () { };
    }

    public function beginTransaction(): void
    {
        call_user_func($this->onBeginTransaction);
    }

    public function commitTransaction(): void
    {
        call_user_func($this->onCommitTransaction);
    }

    public function rollbackTransaction(): void
    {
        call_user_func($this->onRollbackTransaction);
    }
}
