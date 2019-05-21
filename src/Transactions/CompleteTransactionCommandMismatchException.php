<?php

namespace LuKun\Workflow\Transactions;

use Exception;

class CompleteTransactionCommandMismatchException extends Exception
{
    public static function create(): CompleteTransactionCommandMismatchException
    {
        return new CompleteTransactionCommandMismatchException('Trying to complete transaction for different command, than the one, for which the current transaction has been opened.');
    }
}
