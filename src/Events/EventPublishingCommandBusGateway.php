<?php

namespace LuKun\Workflow\Events;

use LuKun\Workflow\Commands\ICommandBusGateway;
use LuKun\Workflow\Commands\Result;

class EventPublishingCommandBusGateway implements ICommandBusGateway
{
    /** @var IEventBus */
    private $eventBus;

    public function __construct(IEventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function commandReceived(object $command): object
    {
        return $command;
    }

    public function commandCompleted(object $command, Result $result): Result
    {
        $result->readEvents(function ($event) {
            $this->eventBus->publish($event);
        });

        return $result;
    }
}
