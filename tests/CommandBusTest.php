<?php

namespace LuKun\Workflow\Tests;

use RuntimeException;
use LuKun\Workflow\Result;
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

    public function test_execute_HandlerExists_NoHooks()
    {
        $command = new FakeCommand();
        $result = Result::createEmpty();

        $handledCommand = null;

        $this->locator->expects($this->once())
            ->method('findCommandHandlerFor')
            ->with(FakeCommand::class)
            ->willReturn(function (FakeCommand $command) use (&$handledCommand, $result) {
                $handledCommand = $command;
                return $result;
            });

        $returnedResult = $this->commandBus->execute($command);

        $this->assertTrue($command == $handledCommand);
        $this->assertTrue($result == $returnedResult);
    }

    public function test_execute_HandlerNotExists_NoHooks()
    {
        $command = new FakeCommand();

        $returnedResult = null;

        $this->locator->expects($this->once())
            ->method('findCommandHandlerFor')
            ->with(FakeCommand::class)
            ->willReturn(null);

        $this->expectException(RuntimeException::class);
        $returnedResult = $this->commandBus->execute($command);

        $this->assertNull($returnedResult);
    }

    public function test_execute_HandlerExists_MultipleHooks()
    {
        $command = new FakeCommand();
        $result = Result::createEmpty();

        $handledCommand = null;

        $this->locator->method('findCommandHandlerFor')
            ->with(FakeCommand::class)
            ->willReturn(function (FakeCommand $command) use (&$handledCommand, $result) {
                $handledCommand = $command;
                return $result;
            });

        $receivedCommands = [];
        $receivedCommandHooks = [];
        $this->commandBus->onCommandReceived(function (FakeCommand $command) use (&$receivedCommands, &$receivedCommandHooks) {
            array_push($receivedCommands, $command);
            array_push($receivedCommandHooks, 'Hook1');
        });
        $this->commandBus->onCommandReceived(function (FakeCommand $command) use (&$receivedCommands, &$receivedCommandHooks) {
            array_push($receivedCommands, $command);
            array_push($receivedCommandHooks, 'Hook2');
        });

        $completedCommandResults = [];
        $completedCommandHooks = [];
        $this->commandBus->onCommandCompleted(function (Result $result) use (&$completedCommandResults, &$completedCommandHooks) {
            array_push($completedCommandResults, $result);
            array_push($completedCommandHooks, 'Hook1');
        });
        $this->commandBus->onCommandCompleted(function (Result $result) use (&$completedCommandResults, &$completedCommandHooks) {
            array_push($completedCommandResults, $result);
            array_push($completedCommandHooks, 'Hook2');
        });

        $returnedResult = $this->commandBus->execute($command);

        $this->assertCount(2, $receivedCommands);
        $this->assertTrue($command == $receivedCommands[0]);
        $this->assertTrue($command == $receivedCommands[1]);
        $this->assertCount(2, $receivedCommandHooks);
        $this->assertTrue($receivedCommandHooks[0] === 'Hook1');
        $this->assertTrue($receivedCommandHooks[1] === 'Hook2');

        $this->assertCount(2, $completedCommandResults);
        $this->assertTrue($result == $completedCommandResults[0]);
        $this->assertTrue($result == $completedCommandResults[1]);
        $this->assertCount(2, $completedCommandHooks);
        $this->assertTrue($completedCommandHooks[0] === 'Hook1');
        $this->assertTrue($completedCommandHooks[1] === 'Hook2');

        $this->assertTrue($command == $handledCommand);
        $this->assertTrue($result == $returnedResult);
    }

    public function test_execute_HandlerNotExists_MultipleHooks()
    {
        $command = new FakeCommand();
        $result = Result::createEmpty();

        $handledCommand = null;

        $this->locator->method('findCommandHandlerFor')
            ->with(FakeCommand::class)
            ->willReturn(null);

        $receivedCommands = [];
        $receivedCommandHooks = [];
        $this->commandBus->onCommandReceived(function (FakeCommand $command) use (&$receivedCommands, &$receivedCommandHooks) {
            array_push($receivedCommands, $command);
            array_push($receivedCommandHooks, 'Hook1');
        });
        $this->commandBus->onCommandReceived(function (FakeCommand $command) use (&$receivedCommands, &$receivedCommandHooks) {
            array_push($receivedCommands, $command);
            array_push($receivedCommandHooks, 'Hook2');
        });

        $completedCommandResults = [];
        $completedCommandHooks = [];
        $this->commandBus->onCommandCompleted(function (Result $result) use (&$completedCommandResults, &$completedCommandHooks) {
            array_push($completedCommandResults, $result);
            array_push($completedCommandHooks, 'Hook1');
        });
        $this->commandBus->onCommandCompleted(function (Result $result) use (&$completedCommandResults, &$completedCommandHooks) {
            array_push($completedCommandResults, $result);
            array_push($completedCommandHooks, 'Hook2');
        });

        $this->expectException(RuntimeException::class);
        $returnedResult = $this->commandBus->execute($command);

        $this->assertCount(2, $receivedCommands);
        $this->assertTrue($command == $receivedCommands[0]);
        $this->assertTrue($command == $receivedCommands[1]);
        $this->assertCount(2, $receivedCommandHooks);
        $this->assertTrue($receivedCommandHooks[0] === 'Hook1');
        $this->assertTrue($receivedCommandHooks[1] === 'Hook2');

        $this->assertCount(0, $completedCommandResults);
        $this->assertCount(0, $completedCommandHooks);

        $this->assertTrue($command == $handledCommand);
        $this->assertNull($returnedResult);
    }
}
