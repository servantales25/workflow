<?php

namespace LuKun\Workflow\Events;

interface IEventHandlerLocator
{
    /** @return callable[] - (object $event): void */
    function findEventHandlersFor(string $event): array;
}
