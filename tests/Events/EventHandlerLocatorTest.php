<?php

namespace LuKun\Workflow\Tests\Events;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Events\EventHandlerLocator;

class EventHandlerLocatorTest extends TestCase
{
    /** @var EventHandlerLocator */
    private $locator;

    protected function setUp(): void
    {
        $this->locator = new EventHandlerLocator();
    }

    public function test_WithoutHandlers()
    {
        $event = 'test';

        $foundHandlers = $this->locator->findEventHandlersFor($event);

        $this->assertSame([], $foundHandlers);
    }

    public function test_WithHandlers_KnownCommand()
    {
        $event = 'test';
        $handler1 = function () { };
        $handler2 = function () { };

        $this->locator->registerHandler($event, $handler1);
        $this->locator->registerHandler($event, $handler2);

        $foundHandlers = $this->locator->findEventHandlersFor($event);

        $this->assertSame([$handler1, $handler2], $foundHandlers);
    }

    public function test_WithHandlers_UnknownCommand()
    {
        $event1 = 'test';
        $event2 = 'test2';
        $handler1 = function () { };

        $this->locator->registerHandler($event1, $handler1);

        $foundHandlers = $this->locator->findEventHandlersFor($event2);

        $this->assertSame([], $foundHandlers);
    }
}
