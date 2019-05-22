<?php

namespace LuKun\Workflow\Tests\Domain\Fakes;

use LuKun\Workflow\Domain\IIdentity;

class FakeIdentity implements IIdentity
{
    /** @var int */
    private $number;

    public function __construct(int $number)
    {
        $this->number = $number;
    }

    /** @param IIdentity $identity */
    public function equalsTo($identity): bool
    {
        return $identity instanceof FakeIdentity
            && $this->number === $identity->number;
    }
}
