<?php

namespace LuKun\Workflow\Domain;

use InvalidArgumentException;

class StringId implements IIdentity
{
    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $value)) {
            throw new InvalidArgumentException('Value must be non-empty string that contains only alphanumeric characters, dash or underscore.');
        }
        $this->value = $value;
    }

    public function equalsTo(IIdentity $identity): bool
    {
        return ($identity instanceof StringId) && $this->value === $identity->value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function fromString(string $value): StringId
    {
        return new StringId($value);
    }
}
