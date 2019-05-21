<?php

namespace LuKun\Workflow\Tests\DI;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Tests\DI\Fakes\FakeServiceLocator;
use LuKun\Workflow\DI\LazyCommandHandlerLocator;

class CommandHandler1
{
    function __invoke()
    { }
}

class CommandHandler2
{
    function __invoke()
    { }
}

class LazyCommandHandlerLocatorTest extends TestCase
{
    /** @var LazyCommandHandlerLocator */
    private $locator;
    /** @var FakeServiceLocator */
    private $serviceLocator;
    /** @var CommandHandler1 */
    private $handler1;
    /** @var CommandHandler2 */
    private $handler2;

    protected function setUp(): void
    {
        $this->serviceLocator = new FakeServiceLocator();
        $this->locator = new LazyCommandHandlerLocator($this->serviceLocator);
        $this->handler1 = new CommandHandler1();
        $this->handler2 = new CommandHandler2();

        $this->serviceLocator->onFindByType = function ($className) {
            switch ($className) {
                case CommandHandler1::class:
                    return $this->handler1;
                case CommandHandler2::class:
                    return $this->handler2;

                default:
                    return null;
            }
        };
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

        $this->locator->registerHandler($command1, CommandHandler1::class);
        $this->locator->registerHandler($command2, CommandHandler2::class);

        $foundHandler1 = $this->locator->findCommandHandlerFor($command1);
        $foundHandler2 = $this->locator->findCommandHandlerFor($command2);

        $this->assertSame($this->handler1, $foundHandler1);
        $this->assertSame($this->handler2, $foundHandler2);
    }

    public function test_WithHandlers_UnknownCommand()
    {
        $command1 = 'test';
        $command2 = 'test2';

        $this->locator->registerHandler($command1, CommandHandler1::class);

        $foundHandler = $this->locator->findCommandHandlerFor($command2);

        $this->assertNull($foundHandler);
    }
}
