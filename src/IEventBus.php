<?php

namespace LuKun\Workflow;

interface IEventBus
{
    function publish(object $event): void;
}
