<?php

namespace LuKun\Workflow\Domain;

use InvalidArgumentException;

class IntId implements IIdentity
{
    /** @var int */
    private $value;

    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new InvalidArgumentException('Value must be simple number above zero.');
        }
        $this->value = $value;
    }

    public function equalsTo(IIdentity $identity): bool
    {
        return ($identity instanceof IntId) && $this->value === $identity->value;
    }

    public function toString(): string
    {
        return strval($this->value);
    }

    public function fromString(string $value): IntId
    {
        return new IntId(intval($value));
    }
}
