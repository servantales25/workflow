<?php

namespace LuKun\Workflow\Tests\Domain\Fakes;

use LuKun\Workflow\Domain\AggregateEntity;
use LuKun\Workflow\Domain\IIdentity;

class FakeAggregateEntity extends AggregateEntity
{
    public function __construct(IIdentity $identity)
    {
        parent::__construct($identity);
    }

    public function collect(object $event): void
    {
        parent::collect($event);
    }
}
