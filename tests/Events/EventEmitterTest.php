<?php

namespace LuKun\Workflow\Tests\Events;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Tests\Events\Fakes\FakeEventEmitter;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent2;

class EventEmitter extends TestCase
{
    /** @var FakeEventEmitter */
    private $emitter;

    protected function setUp(): void
    {
        $this->emitter = new FakeEventEmitter();
    }

    public function test_on()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();
        $event3 = new FakeEvent2();

        $handledEvents1 = [];
        $handledEvents2 = [];

        $handler1 = function ($event) use (&$handledEvents1) {
            array_push($handledEvents1, $event);
        };
        $handler2 = function ($event) use (&$handledEvents2) {
            array_push($handledEvents2, $event);
        };

        $this->emitter->on(FakeEvent::class, $handler1);
        $this->emitter->on(FakeEvent2::class, $handler2);

        $this->emitter->emit($event1);
        $this->emitter->emit($event2);
        $this->emitter->emit($event3);

        $this->assertSame([$event1], $handledEvents1);
        $this->assertSame([$event2, $event3], $handledEvents2);
    }

    public function test_off()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();
        $event3 = new FakeEvent2();

        $handledEvents1 = [];
        $handledEvents2 = [];
        $handledEvents3 = [];

        $handler1 = function ($event) use (&$handledEvents1) {
            array_push($handledEvents1, $event);
        };
        $handler2 = function ($event) use (&$handledEvents2) {
            array_push($handledEvents2, $event);
        };
        $handler3 = function ($event) use (&$handledEvents3) {
            array_push($handledEvents3, $event);
        };

        $this->emitter->on(FakeEvent::class, $handler1);
        $this->emitter->on(FakeEvent2::class, $handler2);
        $this->emitter->on(FakeEvent2::class, $handler3);
        $this->emitter->off(FakeEvent2::class, $handler2);

        $this->emitter->emit($event1);
        $this->emitter->emit($event2);
        $this->emitter->emit($event3);

        $this->assertSame([$event1], $handledEvents1);
        $this->assertSame([], $handledEvents2);
        $this->assertSame([$event2, $event3], $handledEvents3);
    }
}
