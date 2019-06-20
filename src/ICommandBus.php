<?php

namespace LuKun\Workflow;

interface ICommandBus
{
    function execute(object $command): Result;
}
