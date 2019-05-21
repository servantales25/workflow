<?php

namespace LuKun\Workflow\Commands;

interface ICommandBus
{
    function execute(object $command): Result;
}
