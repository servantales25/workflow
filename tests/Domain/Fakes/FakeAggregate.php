<?php

namespace LuKun\Workflow\Tests\Domain\Fakes;

use LuKun\Workflow\Domain\Aggregate;

class FakeAggregate extends Aggregate
{
    public function __construct($id)
    {
        parent::__construct($id);
    }

    public function submit(object $event): void
    {
        parent::submit($event);
    }
}
