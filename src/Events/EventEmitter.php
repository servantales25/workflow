<?php

namespace LuKun\Workflow\Events;

use LuKun\Structures\Collections\HashTable;

class EventEmitter implements IEventEmitter
{
    /** @var HashTable */
    private $eventHandlers;

    public function __construct()
    {
        $this->eventHandlers = new HashTable();
    }

    /** @param callable $eventHandler - (...$args): void */
    public function on(string $event, callable $eventHandler): void
    {
        if (!$this->eventHandlers->containsOf($event, $eventHandler)) {
            $this->eventHandlers->addTo($event, $eventHandler);
        }
    }

    /** @param callable $eventHandler - (...$args): void */
    public function off(string $event, callable $eventHandler): void
    {
        $this->eventHandlers->removeOf($event, $eventHandler);
    }

    public function emit(string $event, ...$args): void
    {
        $this->eventHandlers->walkOf($event, function (callable $handler) use ($args) {
            $handler(...$args);
        });
    }
}
