<?php

namespace LuKun\Workflow\Commands;

interface ICommandBusGateway
{
    function commandReceived(object $command): object;
    function commandCompleted(object $command, Result $result): Result;
}
