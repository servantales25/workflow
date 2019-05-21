<?php

namespace LuKun\Workflow\Tests\Events;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Events\EventHandlerLocator;
use LuKun\Workflow\Events\EventBus;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent2;

class EventBusTest extends TestCase
{
    /** @var EventHandlerLocator */
    private $locator;
    /** @var EventBus */
    private $eventBus;

    protected function setUp(): void
    {
        $this->locator = new EventHandlerLocator();
        $this->eventBus = new EventBus($this->locator);
    }

    public function test_publish_WithHandlers_MultipleEvents()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $handler1event = null;
        $handler2event = null;
        $handler3event = null;
        $handler4event = null;

        $this->locator->registerHandler(FakeEvent::class, function ($event1) use (&$handler1event) {
            $handler1event = $event1;
        });
        $this->locator->registerHandler(FakeEvent::class, function ($event1) use (&$handler2event) {
            $handler2event = $event1;
        });
        $this->locator->registerHandler(FakeEvent2::class, function ($event) use (&$handler3event) {
            $handler3event = $event;
        });
        $this->locator->registerHandler(FakeEvent2::class, function ($event) use (&$handler4event) {
            $handler4event = $event;
        });

        $this->eventBus->publish($event1);

        $this->assertSame($event1, $handler1event);
        $this->assertSame($event1, $handler2event);
        $this->assertNull($handler3event);
        $this->assertNull($handler4event);

        $this->eventBus->publish($event2);

        $this->assertSame($event1, $handler1event);
        $this->assertSame($event1, $handler2event);
        $this->assertSame($event2, $handler3event);
        $this->assertSame($event2, $handler4event);
    }

    public function test_publish_WithoutHandlers_MultipleEvents()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $handler1event = null;
        $handler2event = null;
        $handler3event = null;
        $handler4event = null;

        $this->locator->registerHandler(FakeEvent2::class, function ($event) use (&$handler3event) {
            $handler3event = $event;
        });
        $this->locator->registerHandler(FakeEvent2::class, function ($event) use (&$handler4event) {
            $handler4event = $event;
        });

        $this->eventBus->publish($event1);

        $this->assertNull($handler1event);
        $this->assertNull($handler2event);
        $this->assertNull($handler3event);
        $this->assertNull($handler4event);

        $this->eventBus->publish($event2);

        $this->assertNull($handler1event);
        $this->assertNull($handler2event);
        $this->assertSame($event2, $handler3event);
        $this->assertSame($event2, $handler4event);
    }
}
