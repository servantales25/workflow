<?php

namespace LuKun\Workflow\Commands;

use LuKun\Structures\Collections\Vector;

class CommandBus implements ICommandBus
{
    /** @var ICommandHandlerLocator */
    private $handlerLocator;
    /** @var Vector */
    private $gateways;

    public function __construct(ICommandHandlerLocator $handlerLocator)
    {
        $this->handlerLocator = $handlerLocator;
        $this->gateways = new Vector();
    }

    public function execute(object $command): Result
    {
        $command = $this->_commandReceived($command);
        $handler = $this->_loadHandler($command);
        $result = $handler($command);
        $result = $this->_commandCompleted($command, $result);

        return $result;
    }

    public function addGateway(ICommandBusGateway $gateway): void
    {
        $this->gateways->add($gateway);
    }

    private function _loadHandler(object $command): callable
    {
        $commandClass = get_class($command);
        $handler = $this->handlerLocator->findCommandHandlerFor($commandClass);
        if ($handler === null) {
            throw CommandHandlerNotFoundException::create($commandClass);
        }

        return $handler;
    }

    private function _commandReceived(object $command): object
    {
        $this->gateways->walk(function (ICommandBusGateway $gateway) use (&$command) {
            $command = $gateway->commandReceived($command);
        });

        return $command;
    }

    private function _commandCompleted(object $command, Result $result): Result
    {
        $this->gateways->walkReverse(function (ICommandBusGateway $gateway) use ($command, &$result) {
            $result = $gateway->commandCompleted($command, $result);
        });

        return $result;
    }
}
