<?php

namespace LuKun\Workflow\Tests;

use LuKun\Workflow\EventBus;
use PHPUnit\Framework\TestCase;
use LuKun\Workflow\LazyEventBus;
use LuKun\Workflow\Tests\Fakes\FakeEvent;
use LuKun\Workflow\IEventListenersLocator;
use LuKun\Workflow\Tests\Fakes\FakeEvent2;

class LazyEventBusTest extends TestCase
{
    /** @var IEventListenersLocator */
    private $locator;
    /** @var LazyEventBus */
    private $eventBus;

    protected function setUp(): void
    {
        $this->locator = $this->createMock(IEventListenersLocator::class);
        $this->eventBus = new LazyEventBus($this->locator);
    }

    public function test_publish()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $this->locator->expects($this->never())
            ->method('findEventListenersFor');

        $receivedEvents = [];
        $this->eventBus->on(EventBus::EVENT_RECEIVED, function ($event) use (&$receivedEvents) {
            $receivedEvents[] = $event;
        });

        $publishedEvents = [];
        $this->eventBus->on(EventBus::EVENT_PUBLISHED, function ($event, $listenerIndex) use (&$publishedEvents) {
            $publishedEvents[] = $event;
        });

        $this->eventBus->publish($event1);
        $this->eventBus->publish($event2);

        $this->assertSame([], $receivedEvents);
        $this->assertSame([], $publishedEvents);
    }

    public function test_apply()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $listenerEvents = [];
        $this->locator->method('findEventListenersFor')
            ->withConsecutive($event1, $event1, $event2, $event1)
            ->willReturn([function ($event) use (&$listenerEvents) {
                $listenerEvents[] = $event;
            }]);

        $receivedEvents = [];
        $this->eventBus->on(EventBus::EVENT_RECEIVED, function ($event) use (&$receivedEvents) {
            $receivedEvents[] = $event;
        });

        $publishedEvents = [];
        $this->eventBus->on(EventBus::EVENT_PUBLISHED, function ($event, $listenerIndex) use (&$publishedEvents) {
            $publishedEvents[] = $event;
        });

        $this->eventBus->publish($event1);
        $this->eventBus->publish($event1);
        $this->eventBus->publish($event2);
        $this->eventBus->publish($event1);

        $this->assertSame([], $listenerEvents);
        $this->assertSame([], $receivedEvents);
        $this->assertSame([], $publishedEvents);

        $this->eventBus->apply();

        $this->assertSame([$event1, $event1, $event2, $event1], $listenerEvents);
        $this->assertSame([$event1, $event1, $event2, $event1], $receivedEvents);
        $this->assertSame([$event1, $event1, $event2, $event1], $publishedEvents);

        $listenerEvents = [];
        $receivedEvents = [];
        $publishedEvents = [];

        $this->eventBus->apply();

        $this->assertSame([], $listenerEvents);
        $this->assertSame([], $receivedEvents);
        $this->assertSame([], $publishedEvents);
    }

    public function test_reset()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $this->locator->expects($this->never())
            ->method('findEventListenersFor');

        $receivedEvents = [];
        $this->eventBus->on(EventBus::EVENT_RECEIVED, function ($event) use (&$receivedEvents) {
            $receivedEvents[] = $event;
        });

        $publishedEvents = [];
        $this->eventBus->on(EventBus::EVENT_PUBLISHED, function ($event, $listenerIndex) use (&$publishedEvents) {
            $publishedEvents[] = $event;
        });

        $this->eventBus->publish($event1);
        $this->eventBus->publish($event2);

        $this->eventBus->reset();
        $this->eventBus->apply();

        $this->assertSame([], $receivedEvents);
        $this->assertSame([], $publishedEvents);
    }
}
