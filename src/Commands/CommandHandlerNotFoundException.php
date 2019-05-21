<?php

namespace LuKun\Workflow\Commands;

use Exception;

class CommandHandlerNotFoundException extends Exception
{
    public static function create(string $commandClass): CommandHandlerNotFoundException
    {
        return new CommandHandlerNotFoundException("Handler for command '{$commandClass}' was not found.");
    }
}
