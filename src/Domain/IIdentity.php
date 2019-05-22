<?php

namespace LuKun\Workflow\Domain;

interface IIdentity
{
    /** @param IIdentity $identity */
    function equalsTo($identity): bool;
}
