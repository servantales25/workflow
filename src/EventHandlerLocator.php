<?php

namespace LuKun\Workflow;

use LuKun\Structures\Collections\HashTable;

class EventHandlerLocator implements IEventHandlerLocator
{
    /** @var HashTable */
    private $handlers;

    public function __construct()
    {
        $this->handlers = new HashTable();
    }

    /** @return callable[] */
    public function findEventHandlersFor(string $event): array
    {
        return $this->handlers->toArrayOf($event);
    }

    public function registerHandler(string $event, callable $handler): void
    {
        $this->handlers->addTo($event, $handler);
    }
}
