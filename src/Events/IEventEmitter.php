<?php

namespace LuKun\Workflow\Events;

interface IEventEmitter
{
    /** @param callable $eventHandler - (...$args): void */
    function on(string $event, callable $eventHandler): void;

    /** @param callable $eventHandler - (...$args): void */
    function off(string $event, callable $eventHandler): void;
}
