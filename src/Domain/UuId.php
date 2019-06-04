<?php

namespace LuKun\Workflow\Domain;

use Ramsey\Uuid\Uuid as RamseyUuid;

class UuId implements IIdentity
{
    /** @var RamseyUuid */
    private $value;

    private function __construct(RamseyUuid $value)
    {
        $this->value = $value;
    }

    public function equalsTo(IIdentity $identity): bool
    {
        return ($identity instanceof UuId) && $this->value->equals($identity->value);
    }

    public function toString(): string
    {
        return $this->value->toString();
    }

    public function toRamseyUuid(): RamseyUuid
    {
        return clone $this->value;
    }

    public static function fromString(string $value): UuId
    {
        return new UuId(RamseyUuid::fromString($value));
    }

    public static function fromRamseyUuid(RamseyUuid $value): Uuid
    {
        return new Uuid($value);
    }

    public static function generate(): UuId
    {
        return new UuId(RamseyUuid::uuid4());
    }
}
