<?php

namespace LuKun\Workflow;

interface ICommandHandlerLocator
{
    /** @return null|callable - (object $command): Result */
    function findCommandHandlerFor(string $command): ?callable;
}
