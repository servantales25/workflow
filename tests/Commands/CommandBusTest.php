<?php

namespace LuKun\Workflow\Tests\Commands;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Commands\Result;
use LuKun\Workflow\Commands\CommandHandlerLocator;
use LuKun\Workflow\Commands\CommandBus;
use stdClass;
use LuKun\Workflow\Tests\Commands\Fakes\FakeCommandBusGateway;
use LuKun\Workflow\Commands\CommandHandlerNotFoundException;

class CommandBusTest extends TestCase
{
    /** @var CommandHandlerLocator */
    private $locator;
    /** @var CommandBus */
    private $commandBus;
    /** @var FakeCommandBusGateway */
    private $gateway1;
    /** @var FakeCommandBusGateway */
    private $gateway2;

    protected function setUp(): void
    {
        $this->locator = new CommandHandlerLocator();
        $this->commandBus = new CommandBus($this->locator);
        $this->gateway1 = new FakeCommandBusGateway();
        $this->gateway2 = new FakeCommandBusGateway();
    }

    public function test_execute_WithHandler_WithoutGateways()
    {
        $handlerResult = Result::createEmpty();
        $givenCommand = new stdClass();

        $handledCommand = null;
        $returnedResult = null;
        $this->locator->registerHandler(stdClass::class, function ($givenCommand) use (&$handledCommand, &$handlerResult) {
            $handledCommand = $givenCommand;
            return $handlerResult;
        });

        $returnedResult = $this->commandBus->execute($givenCommand);

        $this->assertSame($givenCommand, $handledCommand);
        $this->assertSame($handlerResult, $returnedResult);
    }

    public function test_execute_WithHandler_WithGateways()
    {
        $handlerResult = Result::createEmpty();
        $givenCommand = new stdClass();
        $gateway1command = new stdClass();
        $gateway1result = Result::createEmpty();
        $gateway2command = new stdClass();
        $gateway2result = Result::createEmpty();

        $this->commandBus->addGateway($this->gateway1);
        $this->commandBus->addGateway($this->gateway2);

        $handledCommand = null;
        $gateway1ReceivedCommandArg = null;
        $gateway2ReceivedCommandArg = null;
        $gateway1CompletedCommandArg = null;
        $gateway1CompletedResultArg = null;
        $gateway2CompletedCommandArg = null;
        $gateway2CompletedResultArg = null;
        $returnedResult = null;

        $this->gateway1->onCommandReceived = function ($command) use (&$gateway1ReceivedCommandArg, &$gateway1command) {
            $gateway1ReceivedCommandArg = $command;
            return $gateway1command;
        };
        $this->gateway2->onCommandReceived = function ($command) use (&$gateway2ReceivedCommandArg, &$gateway2command) {
            $gateway2ReceivedCommandArg = $command;
            return $gateway2command;
        };
        $this->gateway1->onCommandCompleted = function ($command, $result) use (&$gateway1CompletedCommandArg, &$gateway1CompletedResultArg, &$gateway1result) {
            $gateway1CompletedCommandArg = $command;
            $gateway1CompletedResultArg = $result;
            return $gateway1result;
        };
        $this->gateway2->onCommandCompleted = function ($command, $result) use (&$gateway2CompletedCommandArg, &$gateway2CompletedResultArg, &$gateway2result) {
            $gateway2CompletedCommandArg = $command;
            $gateway2CompletedResultArg = $result;
            return $gateway2result;
        };
        $this->locator->registerHandler(stdClass::class, function ($command) use (&$handledCommand, &$handlerResult) {
            $handledCommand = $command;
            return $handlerResult;
        });

        $returnedResult = $this->commandBus->execute($givenCommand);

        $this->assertSame($gateway1ReceivedCommandArg, $givenCommand);
        $this->assertSame($gateway2ReceivedCommandArg, $gateway1command);
        $this->assertSame($handledCommand, $gateway2command);
        $this->assertSame($gateway2CompletedCommandArg, $gateway2command);
        $this->assertSame($gateway2CompletedResultArg, $handlerResult);
        $this->assertSame($gateway1CompletedCommandArg, $gateway2command);
        $this->assertSame($gateway1CompletedResultArg, $gateway2result);
        $this->assertSame($returnedResult, $gateway1result);
    }

    public function test_execute_WithoutHandler_WithoutGateways()
    {
        $givenCommand = new stdClass();

        $returnedResult = null;

        $this->expectException(CommandHandlerNotFoundException::class);
        $returnedResult = $this->commandBus->execute($givenCommand);

        $this->assertNull($returnedResult);
    }

    public function test_execute_WithoutHandler_WithGateways()
    {
        $givenCommand = new stdClass();
        $gateway1command = new stdClass();
        $gateway1result = Result::createEmpty();
        $gateway2command = new stdClass();
        $gateway2result = Result::createEmpty();

        $this->commandBus->addGateway($this->gateway1);
        $this->commandBus->addGateway($this->gateway2);

        $gateway1ReceivedCommandArg = null;
        $gateway2ReceivedCommandArg = null;
        $gateway1CompletedCommandArg = null;
        $gateway1CompletedResultArg = null;
        $gateway2CompletedCommandArg = null;
        $gateway2CompletedResultArg = null;
        $returnedResult = null;

        $this->gateway1->onCommandReceived = function ($command) use (&$gateway1ReceivedCommandArg, &$gateway1command) {
            $gateway1ReceivedCommandArg = $command;
            return $gateway1command;
        };
        $this->gateway2->onCommandReceived = function ($command) use (&$gateway2ReceivedCommandArg, &$gateway2command) {
            $gateway2ReceivedCommandArg = $command;
            return $gateway2command;
        };
        $this->gateway1->onCommandCompleted = function ($command, $result) use (&$gateway1CompletedCommandArg, &$gateway1CompletedResultArg, &$gateway1result) {
            $gateway1CompletedCommandArg = $command;
            $gateway1CompletedResultArg = $result;
            return $gateway1result;
        };
        $this->gateway2->onCommandCompleted = function ($command, $result) use (&$gateway2CompletedCommandArg, &$gateway2CompletedResultArg, &$gateway2result) {
            $gateway2CompletedCommandArg = $command;
            $gateway2CompletedResultArg = $result;
            return $gateway2result;
        };

        $this->expectException(CommandHandlerNotFoundException::class);
        $returnedResult = $this->commandBus->execute($givenCommand);

        $this->assertSame($gateway1ReceivedCommandArg, $givenCommand);
        $this->assertSame($gateway2ReceivedCommandArg, $gateway1command);
        $this->assertNull($gateway2CompletedCommandArg);
        $this->assertNull($gateway2CompletedResultArg);
        $this->assertNull($gateway1CompletedCommandArg);
        $this->assertNull($gateway1CompletedResultArg);
        $this->assertNull($returnedResult);
    }
}
