<?php

namespace LuKun\Workflow\Tests;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\EventListenersLocator;

class EventListenersLocatorTest extends TestCase
{
    /** @var EventListenersLocator */
    private $locator;

    protected function setUp(): void
    {
        $this->locator = new EventListenersLocator();
    }

    public function test_WithoutListeners()
    {
        $event = 'test';

        $foundListeners = $this->locator->findEventListenersFor($event);

        $this->assertSame([], $foundListeners);
    }

    public function test_WithListeners_KnownCommand()
    {
        $event = 'test';
        $listener1 = function () { };
        $listener2 = function () { };

        $this->locator->registerListener($event, $listener1);
        $this->locator->registerListener($event, $listener2);

        $foundListeners = $this->locator->findEventListenersFor($event);

        $this->assertSame([$listener1, $listener2], $foundListeners);
    }

    public function test_WithListeners_UnknownCommand()
    {
        $event1 = 'test';
        $event2 = 'test2';
        $listener1 = function () { };

        $this->locator->registerListener($event1, $listener1);

        $foundListeners = $this->locator->findEventListenersFor($event2);

        $this->assertSame([], $foundListeners);
    }
}
