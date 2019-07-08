<?php

namespace LuKun\Workflow;

class LazyEventBus extends EventBus
{
    /** @var object[] */
    private $events;

    public function __construct(IEventListenersLocator $listenersLocator)
    {
        parent::__construct($listenersLocator);

        $this->events = [];
    }

    public function publish(object $event): void
    {
        $this->events[] = $event;
    }

    public function apply(): void
    {
        foreach ($this->events as $event) {
            parent::publish($event);
        }

        $this->events = [];
    }

    public function reset(): void
    {
        $this->events = [];
    }
}
