<?php

namespace LuKun\Workflow\Commands;

interface ICommandHandlerLocator
{
    /** @return null|callable - (object $command): Result */
    function findCommandHandlerFor(string $command): ?callable;
}
