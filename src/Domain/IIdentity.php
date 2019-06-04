<?php

namespace LuKun\Workflow\Domain;

interface IIdentity
{
    function equalsTo(IIdentity $identity): bool;
    function toString(): string;
}
