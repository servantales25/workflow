<?php

namespace LuKun\Workflow;

use LuKun\Events\EventEmitter;
use RuntimeException;
use Throwable;

class CommandBus extends EventEmitter
{
    /** (object $command): void */
    public const COMMAND_RECEIVED = 'commandReceived';
    /** (object $command, Throwable $exception): void */
    public const COMMAND_HANDLING_FAILED = 'commandHandlingFailed';
    /** (object $command, Result $result): void */
    public const COMMAND_EXECUTED = 'commandExecuted';

    /** @var ICommandHandlerLocator */
    private $handlerLocator;

    public function __construct(ICommandHandlerLocator $handlerLocator)
    {
        parent::__construct();

        $this->handlerLocator = $handlerLocator;
    }

    public function execute(object $command): void
    {
        $this->submit(self::COMMAND_RECEIVED, $command);

        $handler = $this->_loadHandler($command);

        try {
            call_user_func($handler, $command);
        } catch (Throwable $e) {
            $this->submit(self::COMMAND_HANDLING_FAILED, $command, $e);
        }

        $this->submit(self::COMMAND_EXECUTED, $command);
    }

    private function _loadHandler(object $command): callable
    {
        $commandClass = get_class($command);
        $handler = $this->handlerLocator->findCommandHandlerFor($commandClass);
        if (null === $handler) {
            throw new RuntimeException("Handler for command '{$commandClass}' was not found.");
        }

        return $handler;
    }
}
