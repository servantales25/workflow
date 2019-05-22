<?php

namespace LuKun\Workflow\Domain;

use LuKun\Structures\Collections\Vector;

abstract class AggregateEntity extends Entity
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

    /** @param callable $handleEvent (object $event): void */
    public function readEvents(callable $handleEvent): void
    {
        $this->events->walk($handleEvent);
    }

    public function dropEvents(): void
    {
        $this->events->clear();
    }

    protected function collect(object $event): void
    {
        $this->events->add($event);
    }
}
