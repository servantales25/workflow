<?php

namespace LuKun\Workflow\Tests\Domain;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Tests\Domain\Fakes\FakeAggregate;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent2;

class AggregateTest extends TestCase
{
    public function test_recordThat()
    {
        $id = 1;
        $aggregate = new FakeAggregate($id);
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $aggregate->recordThat($event1);
        $aggregate->recordThat($event2);

        $readEvents = [];
        $aggregate->readEvents(function ($event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $expectedEvents = [$event1, $event2];
        $this->assertSame($expectedEvents, $readEvents);
    }

    public function test_clearEvents()
    {
        $id = 1;
        $aggregate = new FakeAggregate($id);
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $aggregate->recordThat($event1);
        $aggregate->recordThat($event2);

        $aggregate->clearEvents();

        $readEvents = [];
        $aggregate->readEvents(function ($event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $expectedEvents = [];
        $this->assertSame($expectedEvents, $readEvents);
    }
}
