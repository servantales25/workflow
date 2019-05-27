<?php

namespace LuKun\Workflow\Domain;

use LuKun\Structures\Collections\Vector;

abstract class Aggregate extends Entity
{
    /** @var Vector */
    private $events;

    protected function __construct($id)
    {
        parent::__construct($id);

        $this->events = new Vector();
    }

    /** @param callable $handleEvent - (object $event): void */
    public function readEvents(callable $handleEvent): void
    {
        $this->events->walk($handleEvent);
    }

    public function clearEvents(): void
    {
        $this->events->clear();
    }

    protected function recordThat(object $event): void
    {
        $this->events->add($event);
    }
}
