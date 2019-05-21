<?php

namespace LuKun\Workflow\Events;

interface IEventBus
{
    function publish(object $event): void;
}
