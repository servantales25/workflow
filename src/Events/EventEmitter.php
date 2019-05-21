<?php

namespace LuKun\Workflow\Events;

use LuKun\Structures\Collections\HashTable;

abstract class EventEmitter
{
    /** @var HashTable */
    private $eventHandlers;

    protected function __construct()
    {
        $this->eventHandlers = new HashTable();
    }

    public function on(string $eventClass, callable $eventHandler): void
    {
        if (!$this->eventHandlers->containsOf($eventClass, $eventHandler)) {
            $this->eventHandlers->addTo($eventClass, $eventHandler);
        }
    }

    public function off(string $eventClass, callable $eventHandler): void
    {
        $this->eventHandlers->removeOf($eventClass, $eventHandler);
    }

    protected function emit(object $event): void
    {
        $eventClass = get_class($event);
        $this->eventHandlers->walkOf($eventClass, function (callable $handler) use ($event) {
            $handler($event);
        });
    }
}
