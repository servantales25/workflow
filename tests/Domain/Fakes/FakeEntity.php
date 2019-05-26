<?php

namespace LuKun\Workflow\Tests\Domain\Fakes;

use LuKun\Workflow\Domain\Entity;

class FakeEntity extends Entity
{
    public function __construct($id)
    {
        parent::__construct($id);
    }
}
