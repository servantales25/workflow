<?php

namespace LuKun\Workflow;

use InvalidArgumentException;

class CommandHandlerLocator implements ICommandHandlerLocator
{
    /** @var array<string, callable> */
    private $handlers;

    public function __construct()
    {
        $this->handlers = [];
    }

    public function findCommandHandlerFor(string $command): ?callable
    {
        return $this->handlers[$command] ?? null;
    }

    public function registerHandler(string $command, callable $handler): void
    {
        if (isset($this->handlers[$command])) {
            throw new InvalidArgumentException("Handler for command '{$command}' has been already registered.");
        }

        $this->handlers[$command] = $handler;
    }
}
