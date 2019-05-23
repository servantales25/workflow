<?php

namespace LuKun\Workflow\Tests\Events;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Events\EventEmitter;

class EventEmitterTest extends TestCase
{
    /** @var EventEmitter */
    private $emitter;

    protected function setUp(): void
    {
        $this->emitter = new EventEmitter();
    }

    public function test_on()
    {
        $event1 = 'event1';
        $event2 = 'event2';

        $handledEvents = [];

        $handler1 = function () use ($event1, &$handledEvents) {
            array_push($handledEvents, $event1);
        };
        $handler2 = function () use ($event2, &$handledEvents) {
            array_push($handledEvents, $event2);
        };

        $this->emitter->on($event1, $handler1);
        $this->emitter->on($event2, $handler2);

        $this->emitter->emit($event1);
        $this->emitter->emit($event2);
        $this->emitter->emit($event2);
        $this->emitter->emit($event1);

        $this->assertSame([$event1, $event2, $event2, $event1], $handledEvents);
    }

    public function test_off()
    {
        $event1 = 'event1';
        $event2 = 'event2';

        $handledEvents = [];

        $handler1 = function () use ($event1, &$handledEvents) {
            array_push($handledEvents, $event1);
        };
        $handler2 = function () use ($event2, &$handledEvents) {
            array_push($handledEvents, $event2);
        };

        $this->emitter->on($event1, $handler1);
        $this->emitter->on($event2, $handler2);
        $this->emitter->off($event1, $handler1);

        $this->emitter->emit($event1);
        $this->emitter->emit($event2);
        $this->emitter->emit($event2);
        $this->emitter->emit($event1);

        $this->assertSame([$event2, $event2], $handledEvents);
    }

    public function test_emit_WithArguments()
    {
        $event1 = 'event1';
        $arg1 = 1;
        $arg2 = 2;

        $handler1arg1 = null;
        $handler1arg2 = null;

        $handler1 = function ($arg1, $arg2) use (&$handler1arg1, &$handler1arg2) {
            $handler1arg1 = $arg1;
            $handler1arg2 = $arg2;
        };

        $this->emitter->on($event1, $handler1);

        $this->emitter->emit($event1, $arg1, $arg2);

        $this->assertSame($arg1, $handler1arg1);
        $this->assertSame($arg2, $handler1arg2);
    }
}
