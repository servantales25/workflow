<?php

namespace LuKun\Workflow\Domain;

use LuKun\Workflow\Events\EventEmitter;

abstract class Aggregate extends Entity
{
    /** @var EventEmitter */
    private $eventEmitter;

    public function __construct(IIdentity $identity)
    {
        parent::__construct($identity);

        $this->eventEmitter = new EventEmitter();
    }

    /** @param callable $action - (Aggregate $aggregate, object $event): void */
    public function onChange(callable $action): void
    {
        $this->eventEmitter->on('change', $action);
    }

    protected function publishChange(object $event): void
    {
        $this->eventEmitter->emit('change', $this, $event);
    }
}
