<?php

namespace LuKun\Workflow\Commands;

use LuKun\Structures\Collections\Map;
use InvalidArgumentException;

class CommandHandlerLocator implements ICommandHandlerLocator
{
    /** @var Map */
    private $handlers;

    public function __construct()
    {
        $this->handlers = new Map();
    }

    public function findCommandHandlerFor(string $command): ?callable
    {
        return $this->handlers->get($command);
    }

    public function registerHandler(string $command, callable $handler): void
    {
        if ($this->handlers->has($command)) {
            throw new InvalidArgumentException("Handler for command '{$command}' has been already registered.");
        }

        $this->handlers->set($command, $handler);
    }
}
