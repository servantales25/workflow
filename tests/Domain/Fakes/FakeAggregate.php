<?php

namespace LuKun\Workflow\Tests\Domain\Fakes;

use LuKun\Workflow\Domain\Aggregate;
use LuKun\Workflow\Domain\IIdentity;

class FakeAggregate extends Aggregate
{
    public function __construct(IIdentity $identity)
    {
        parent::__construct($identity);
    }

    public function collectEvent(object $event): void
    {
        parent::collectEvent($event);
    }
}