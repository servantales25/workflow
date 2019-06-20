<?php

namespace LuKun\Workflow\Tests;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\CommandHandlerLocator;

class CommandHandlerLocatorTest extends TestCase
{
    /** @var CommandHandlerLocator */
    private $locator;

    protected function setUp(): void
    {
        $this->locator = new CommandHandlerLocator();
    }

    public function test_WithoutHandlers()
    {
        $command = 'test';

        $foundHandler = $this->locator->findCommandHandlerFor($command);

        $this->assertNull($foundHandler);
    }

    public function test_WithHandlers_KnownCommand()
    {
        $command1 = 'test';
        $command2 = 'test2';
        $handler1 = function () { };
        $handler2 = function () { };

        $this->locator->registerHandler($command1, $handler1);
        $this->locator->registerHandler($command2, $handler2);

        $foundHandler1 = $this->locator->findCommandHandlerFor($command1);
        $foundHandler2 = $this->locator->findCommandHandlerFor($command2);

        $this->assertSame($handler1, $foundHandler1);
        $this->assertSame($handler2, $foundHandler2);
    }

    public function test_WithHandlers_UnknownCommand()
    {
        $command1 = 'test';
        $command2 = 'test2';
        $handler1 = function () { };

        $this->locator->registerHandler($command1, $handler1);

        $foundHandler = $this->locator->findCommandHandlerFor($command2);

        $this->assertNull($foundHandler);
    }
}
