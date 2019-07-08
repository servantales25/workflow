<?php

namespace LuKun\Workflow;

interface IEventListenersLocator
{
    /** @return callable[] - (object $event): void */
    public function findEventListenersFor(string $event): array;
}
