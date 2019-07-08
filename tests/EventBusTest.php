<?php

namespace LuKun\Workflow\Tests;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\IEventListenersLocator;
use LuKun\Workflow\EventBus;
use LuKun\Workflow\Tests\Fakes\FakeEvent;
use LuKun\Workflow\Tests\Fakes\FakeEvent2;

class EventBusTest extends TestCase
{
    /** @var IEventListenersLocator */
    private $locator;
    /** @var EventBus */
    private $eventBus;

    protected function setUp(): void
    {
        $this->locator = $this->createMock(IEventListenersLocator::class);
        $this->eventBus = new EventBus($this->locator);
    }

    public function test_publish_WithListeners()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $listenerEvents = [];
        $this->locator->method('findEventListenersFor')
            ->withConsecutive($event1, $event2)
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
        $this->eventBus->publish($event2);

        $this->assertSame([$event1, $event2], $listenerEvents);
        $this->assertSame([$event1, $event2], $receivedEvents);
        $this->assertSame([$event1, $event2], $publishedEvents);
    }

    public function test_publish_WithoutListeners()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $this->locator->method('findEventListenersFor')
            ->withConsecutive($event1, $event2)
            ->willReturn([]);

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

        $this->assertSame([$event1, $event2], $receivedEvents);
        $this->assertSame([], $publishedEvents);
    }
}
