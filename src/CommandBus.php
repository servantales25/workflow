<?php

namespace LuKun\Workflow;

use Throwable;
use RuntimeException;
use LuKun\Events\EventEmitter;

class CommandBus implements ICommandBus
{
    /** @var ICommandHandlerLocator */
    private $handlerLocator;
    /** @var EventEmitter */
    private $eventEmitter;

    /** @var bool */
    private $inProgress;

    public function __construct(ICommandHandlerLocator $handlerLocator)
    {
        $this->handlerLocator = $handlerLocator;
        $this->eventEmitter = new EventEmitter();

        $this->inProgress = false;
    }

    /** @param callable $action - (object $command): void */
    public function onCommandReceived(callable $action): void
    {
        $this->eventEmitter->on('commandReceived', $action);
    }

    /** @param callable $action - (Result $result): void */
    public function onCommandCompleted(callable $action): void
    {
        $this->eventEmitter->on('commandCompleted', $action);
    }

    public function execute(object $command): Result
    {
        $this->_commandReceived($command);

        $handler = $this->_loadHandler($command);
        $result = Result::createEmpty();
        try {
            $result = call_user_func($handler, $command);
        } catch (Throwable $e) {
            $result = Result::createFailure($e);
        }

        $this->_commandCompleted($result);

        return $result;
    }

    private function _loadHandler(object $command): callable
    {
        $commandClass = get_class($command);
        $handler = $this->handlerLocator->findCommandHandlerFor($commandClass);
        if ($handler === null) {
            throw new RuntimeException("Handler for command '{$commandClass}' was not found.");
        }

        return $handler;
    }

    private function _commandReceived(object $command): void
    {
        if ($this->inProgress) {
            throw new RuntimeException('Another command execution is currently in progress.');
        }

        $this->inProgress = true;
        $this->eventEmitter->emit('commandReceived', $command);
    }

    private function _commandCompleted(Result $result): void
    {
        $this->inProgress = false;
        $this->eventEmitter->emit('commandCompleted', $result);
    }
}
