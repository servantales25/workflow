<?php

namespace LuKun\Workflow\Domain;

abstract class Value
{
    public function equalsTo(Value $value): bool
    {
        return $this == $value;
    }
}
