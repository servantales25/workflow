<?php

namespace LuKun\Workflow;

class EventListenersLocator implements IEventListenersLocator
{
    /** @var array<string, callable[]> */
    private $listeners;

    public function __construct()
    {
        $this->listeners = [];
    }

    /** @return callable[] */
    public function findEventListenersFor(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }

    public function registerListener(string $event, callable $listener): void
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;
    }
}
