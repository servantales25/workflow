<?php

namespace LuKun\Workflow;

interface ICommandHandlerLocator
{
    /** @return null|callable - (object $command): void */
    public function findCommandHandlerFor(string $command): ?callable;
}
