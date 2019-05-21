<?php

namespace LuKun\Workflow\Transactions;

use Exception;

class AnotherCommandTransactionInProgressException extends Exception
{
    public static function create(): AnotherCommandTransactionInProgressException
    {
        return new AnotherCommandTransactionInProgressException('Trying to begin transaction for multiple commands.');
    }
}
