<?php

namespace LuKun\Workflow\Tests\Domain;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Tests\Domain\Fakes\FakeAggregate;
use LuKun\Workflow\Tests\Domain\Fakes\FakeIdentity;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent2;

class AggregateTest extends TestCase
{
    public function test_collect()
    {
        $identity = new FakeIdentity(20);
        $aggregate = new FakeAggregate($identity);
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $aggregate->collect($event1);
        $aggregate->collect($event2);

        $readEvents = [];
        $aggregate->processEvents(function ($event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $expectedEvents = [$event1, $event2];
        $this->assertSame($expectedEvents, $readEvents);
    }

    public function test_processEvents_MultipleTimes()
    {
        $identity = new FakeIdentity(20);
        $aggregate = new FakeAggregate($identity);
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $aggregate->collect($event1);
        $aggregate->collect($event2);

        $readEvents = [];
        $aggregate->processEvents(function () { });
        $aggregate->processEvents(function ($event) use (&$readEvents) {
            array_push($readEvents, $event);
        });
        $modified = $aggregate->isModified();

        $expectedEvents = [];
        $this->assertSame($expectedEvents, $readEvents);
        $this->assertFalse($modified);
    }

    public function test_isModified_WithoutEvents()
    {
        $identity = new FakeIdentity(20);
        $aggregate = new FakeAggregate($identity);

        $modified = $aggregate->isModified();

        $this->assertFalse($modified);
    }

    public function test_isModified_WithEvents()
    {
        $identity = new FakeIdentity(20);
        $aggregate = new FakeAggregate($identity);
        $event = new FakeEvent();

        $aggregate->collect($event);

        $modified = $aggregate->isModified();

        $this->assertTrue($modified);
    }
}
