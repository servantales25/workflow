<?php

namespace LuKun\Workflow\DI;

use LuKun\Structures\Collections\Map;
use LuKun\Workflow\Commands\ICommandHandlerLocator;

class LazyCommandHandlerLocator implements ICommandHandlerLocator
{
    /** @var IServiceLocator */
    private $serviceLocator;
    /** @var Map */
    private $handlers;

    public function __construct(IServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->handlers = new Map();
    }

    public function findCommandHandlerFor(string $command): ?callable
    {
        $handler = null;
        /** @var string|null $handlerClass */
        $handlerClass = $this->handlers->get($command);
        if ($handlerClass !== null) {
            $handler = $this->serviceLocator->findByType($handlerClass);
        }

        return $handler;
    }

    public function registerHandler(string $command, string $handlerClass): void
    {
        if ($this->handlers->has($command)) {
            throw new InvalidArgumentException("Handler for command '{$command}' has been already registered.");
        }

        $this->handlers->set($command, $handlerClass);
    }
}
