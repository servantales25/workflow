<?php

namespace LuKun\Workflow\Tests\DI;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\DI\LazyEventHandlerLocator;
use LuKun\Workflow\Tests\DI\Fakes\FakeServiceLocator;

class EventHandler1
{
    function __invoke()
    { }
}

class EventHandler2
{
    function __invoke()
    { }
}

class LazyEventHandlerLocatorTest extends TestCase
{
    /** @var LazyEventHandlerLocator */
    private $locator;
    /** @var FakeServiceLocator */
    private $serviceLocator;
    /** @var EventHandler1 */
    private $handler1;
    /** @var EventHandler2 */
    private $handler2;

    protected function setUp(): void
    {
        $this->serviceLocator = new FakeServiceLocator();
        $this->locator = new LazyEventHandlerLocator($this->serviceLocator);
        $this->handler1 = new EventHandler1();
        $this->handler2 = new EventHandler2();

        $this->serviceLocator->onFindByType = function ($className) {
            switch ($className) {
                case EventHandler1::class:
                    return $this->handler1;
                case EventHandler2::class:
                    return $this->handler2;

                default:
                    return null;
            }
        };
    }

    public function test_WithoutHandlers()
    {
        $event = 'test';

        $foundHandlers = $this->locator->findEventHandlersFor($event);

        $this->assertSame([], $foundHandlers);
    }

    public function test_WithHandlers_KnownEvent()
    {
        $event = 'test';

        $this->locator->registerHandler($event, EventHandler1::class);
        $this->locator->registerHandler($event, EventHandler2::class);

        $foundHandlers = $this->locator->findEventHandlersFor($event);

        $this->assertSame([$this->handler1, $this->handler2], $foundHandlers);
    }

    public function test_WithHandlers_UnknownEvent()
    {
        $event1 = 'test';
        $event2 = 'test2';

        $this->locator->registerHandler($event1, EventHandler1::class);
        $this->locator->registerHandler($event1, EventHandler2::class);

        $foundHandlers = $this->locator->findEventHandlersFor($event2);

        $this->assertSame([], $foundHandlers);
    }
}
