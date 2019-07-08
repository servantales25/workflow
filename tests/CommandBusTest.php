<?php

namespace LuKun\Workflow\Tests;

use RuntimeException;
use LuKun\Workflow\CommandBus;
use PHPUnit\Framework\TestCase;
use LuKun\Workflow\CommandHandlerLocator;
use LuKun\Workflow\Tests\Fakes\FakeCommand;
use LuKun\Workflow\ICommandHandlerLocator;

class CommandBusTest extends TestCase
{
    /** @var CommandHandlerLocator */
    private $locator;
    /** @var CommandBus */
    private $commandBus;

    protected function setUp(): void
    {
        $this->locator = $this->createMock(ICommandHandlerLocator::class);
        $this->commandBus = new CommandBus($this->locator);
    }

    public function test_execute_HandlerExists()
    {
        $command = new FakeCommand();

        $handledCommand = null;

        $this->locator->method('findCommandHandlerFor')
            ->with(FakeCommand::class)
            ->willReturn(function (FakeCommand $command) use (&$handledCommand) {
                $handledCommand = $command;
            });

        $receivedCommands = [];
        $receivedCommandHooks = [];
        $this->commandBus->on(CommandBus::COMMAND_RECEIVED, function (FakeCommand $command) use (&$receivedCommands, &$receivedCommandHooks) {
            array_push($receivedCommands, $command);
            array_push($receivedCommandHooks, 'ReceivedHook1');
        });
        $this->commandBus->on(CommandBus::COMMAND_RECEIVED, function (FakeCommand $command) use (&$receivedCommands, &$receivedCommandHooks) {
            array_push($receivedCommands, $command);
            array_push($receivedCommandHooks, 'ReceivedHook2');
        });

        $executedCommands = [];
        $executedCommandHooks = [];
        $this->commandBus->on(CommandBus::COMMAND_EXECUTED, function (FakeCommand $command) use (&$executedCommands, &$executedCommandHooks) {
            array_push($executedCommands, $command);
            array_push($executedCommandHooks, 'ExecutedHook1');
        });
        $this->commandBus->on(CommandBus::COMMAND_EXECUTED, function (FakeCommand $command) use (&$executedCommands, &$executedCommandHooks) {
            array_push($executedCommands, $command);
            array_push($executedCommandHooks, 'ExecutedHook2');
        });

        $this->commandBus->execute($command);

        $this->assertCount(2, $receivedCommands);
        $this->assertTrue($command == $receivedCommands[0]);
        $this->assertTrue($command == $receivedCommands[1]);
        $this->assertCount(2, $receivedCommandHooks);
        $this->assertTrue($receivedCommandHooks[0] === 'ReceivedHook1');
        $this->assertTrue($receivedCommandHooks[1] === 'ReceivedHook2');

        $this->assertCount(2, $executedCommands);
        $this->assertTrue($command == $executedCommands[0]);
        $this->assertTrue($command == $executedCommands[1]);
        $this->assertCount(2, $executedCommandHooks);
        $this->assertTrue($executedCommandHooks[0] === 'ExecutedHook1');
        $this->assertTrue($executedCommandHooks[1] === 'ExecutedHook2');

        $this->assertTrue($command == $handledCommand);
    }

    public function test_execute_HandlerNotExists()
    {
        $command = new FakeCommand();

        $handledCommand = null;

        $this->locator->method('findCommandHandlerFor')
            ->with(FakeCommand::class)
            ->willReturn(null);

        $receivedCommands = [];
        $receivedCommandHooks = [];
        $this->commandBus->on(CommandBus::COMMAND_RECEIVED, function (FakeCommand $command) use (&$receivedCommands, &$receivedCommandHooks) {
            array_push($receivedCommands, $command);
            array_push($receivedCommandHooks, 'ReceivedHook1');
        });
        $this->commandBus->on(CommandBus::COMMAND_RECEIVED, function (FakeCommand $command) use (&$receivedCommands, &$receivedCommandHooks) {
            array_push($receivedCommands, $command);
            array_push($receivedCommandHooks, 'ReceivedHook2');
        });

        $executedCommands = [];
        $executedCommandHooks = [];
        $this->commandBus->on(CommandBus::COMMAND_EXECUTED, function (FakeCommand $command) use (&$executedCommands, &$executedCommandHooks) {
            array_push($executedCommands, $command);
            array_push($executedCommandHooks, 'ExecutedHook1');
        });
        $this->commandBus->on(CommandBus::COMMAND_EXECUTED, function (FakeCommand $command) use (&$executedCommands, &$executedCommandHooks) {
            array_push($executedCommands, $command);
            array_push($executedCommandHooks, 'ExecutedHook2');
        });

        $this->expectException(RuntimeException::class);
        $this->commandBus->execute($command);

        $this->assertCount(2, $receivedCommands);
        $this->assertTrue($command == $receivedCommands[0]);
        $this->assertTrue($command == $receivedCommands[1]);
        $this->assertCount(2, $receivedCommandHooks);
        $this->assertTrue($receivedCommandHooks[0] === 'ReceivedHook1');
        $this->assertTrue($receivedCommandHooks[1] === 'ReceivedHook2');

        $this->assertCount(0, $executedCommands);
        $this->assertCount(0, $executedCommandHooks);

        $this->assertNull($handledCommand);
    }
}
