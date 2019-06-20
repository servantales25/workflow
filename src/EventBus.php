<?php

namespace LuKun\Workflow;

class EventBus implements IEventBus
{
    /** @var IEventHandlerLocator */
    private $handlerLocator;

    public function __construct(IEventHandlerLocator $handlerLocator)
    {
        $this->handlerLocator = $handlerLocator;
    }

    public function publish(object $event): void
    {
        $eventClass = get_class($event);
        /** @var callable[] $handlers */
        $handlers = $this->handlerLocator->findEventHandlersFor($eventClass);
        foreach ($handlers as $handler) {
            $handler($event);
        }
    }
}
