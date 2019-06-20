<?php

namespace LuKun\Workflow;

interface IEventHandlerLocator
{
    /** @return callable[] - (object $event): void */
    function findEventHandlersFor(string $event): array;
}
