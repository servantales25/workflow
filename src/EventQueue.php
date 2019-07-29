<?php

namespace LuKun\Workflow;

class EventQueue
{
    /** @var EventBus */
    private $eventBus;

    /** @var object[] */
    private $events;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;

        $this->events = [];
    }

    public function add(object $event): void
    {
        $this->events[] = $event;
    }

    public function release(): void
    {
        foreach ($this->events as $event) {
            $this->eventBus->publish($event);
        }

        $this->events = [];
    }

    public function clear(): void
    {
        $this->events = [];
    }
}
