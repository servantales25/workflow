<?php

namespace LuKun\Workflow\Transactions;

use LuKun\Workflow\Commands\ICommandBusGateway;
use LuKun\Workflow\Commands\Result;

class TransactionalCommandBusGateway implements ICommandBusGateway
{
    /** @var ITransactionManager */
    private $transactionManager;
    /** @var object|null */
    private $currentCommand;

    public function __construct(ITransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
        $this->currentCommand = null;
    }

    public function commandReceived(object $command): object
    {
        if ($this->currentCommand !== null) {
            throw AnotherCommandTransactionInProgressException::create();
        }

        $this->transactionManager->beginTransaction();
        $this->currentCommand = $command;

        return $this->currentCommand;
    }

    public function commandCompleted(object $command, Result $result): Result
    {
        if ($command !== $this->currentCommand) {
            $this->transactionManager->rollbackTransaction();
            throw CompleteTransactionCommandMismatchException::create();
        }

        if ($result->isOk()) {
            $this->transactionManager->commitTransaction();
        } else {
            $this->transactionManager->rollbackTransaction();
        }

        $this->currentCommand = null;

        return $result;
    }
}
