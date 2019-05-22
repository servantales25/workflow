<?php

namespace LuKun\Workflow\Domain;

use LuKun\Structures\Collections\Vector;

abstract class Aggregate extends Entity
{
    /** @var Vector */
    private $events;

    protected function __construct(IIdentity $identity)
    {
        parent::__construct($identity);

        $this->events = new Vector();
    }

    public function isModified(): bool
    {
        return !$this->events->isEmpty();
    }

    /** @param callable $processEvent - (object $event): void */
    public function processEvents(callable $processEvent): void
    {
        $this->events->walk($processEvent);
        $this->events->clear();
    }

    protected function collect(object $event): void
    {
        $this->events->add($event);
    }
}
