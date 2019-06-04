<?php

namespace LuKun\Workflow\Domain;

use LuKun\Workflow\Events\EventEmitter;

abstract class Aggregate extends Entity
{
    /** @var EventEmitter */
    private $eventEmitter;

    public function __construct(IIdentity $id)
    {
        parent::__construct($id);

        $this->eventEmitter = new EventEmitter();
    }

    /** @param callable $action - (object $event): void */
    public function onChange(callable $action): void
    {
        $this->eventEmitter->on('change', $action);
    }

    protected function submit(object $event): void
    {
        $this->eventEmitter->emit('change', $event);
    }
}
