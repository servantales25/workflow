<?php

namespace LuKun\Workflow\Tests\Commands\Fakes;

use LuKun\Workflow\Commands\ICommandBusGateway;
use LuKun\Workflow\Commands\Result;

class FakeCommandBusGateway implements ICommandBusGateway
{
    /** @var callable - (object $command): object */
    public $onCommandReceived;
    /** @var callable - (object $command, Result $result): Result */
    public $onCommandCompleted;

    public function __construct()
    {
        $this->onCommandReceived = function ($command) {
            return $command;
        };
        $this->onCommandCompleted = function ($command, $result) {
            return $result;
        };
    }

    public function commandReceived(object $command): object
    {
        return call_user_func($this->onCommandReceived, $command);
    }

    public function commandCompleted(object $command, Result $result): Result
    {
        return call_user_func($this->onCommandCompleted, $command, $result);
    }
}
