<?php

namespace LuKun\Workflow\Tests\Domain\Fakes;

use LuKun\Workflow\Domain\Entity;
use LuKun\Workflow\Domain\IIdentity;

class FakeEntity extends Entity
{
    public function __construct(IIdentity $identity)
    {
        parent::__construct($identity);
    }
}
