<?php

namespace LuKun\Workflow\Tests\Domain;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Tests\Domain\Fakes\FakeAggregateEntity;
use LuKun\Workflow\Tests\Domain\Fakes\FakeIdentity;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent2;

class AggregateEntityTest extends TestCase
{
    public function test_collect()
    {
        $identity = new FakeIdentity(20);
        $aggregate = new FakeAggregateEntity($identity);
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $aggregate->collect($event1);
        $aggregate->collect($event2);

        $readEvents = [];
        $aggregate->readEvents(function ($event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $expectedEvents = [$event1, $event2];
        $this->assertSame($expectedEvents, $readEvents);
    }

    public function test_dropEvents()
    {
        $identity = new FakeIdentity(20);
        $aggregate = new FakeAggregateEntity($identity);
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $aggregate->collect($event1);
        $aggregate->collect($event2);
        $aggregate->dropEvents();

        $readEvents = [];
        $aggregate->readEvents(function ($event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $expectedEvents = [];
        $this->assertSame($expectedEvents, $readEvents);
    }

    public function test_isModified_WithoutEvents()
    {
        $identity = new FakeIdentity(20);
        $aggregate = new FakeAggregateEntity($identity);

        $modified = $aggregate->isModified();

        $this->assertFalse($modified);
    }

    public function test_isModified_WithEvents()
    {
        $identity = new FakeIdentity(20);
        $aggregate = new FakeAggregateEntity($identity);
        $event = new FakeEvent();

        $aggregate->collect($event);

        $modified = $aggregate->isModified();

        $this->assertTrue($modified);
    }

    public function test_isModified_AfterDropEvents()
    {
        $identity = new FakeIdentity(20);
        $aggregate = new FakeAggregateEntity($identity);
        $event = new FakeEvent();

        $aggregate->collect($event);
        $aggregate->dropEvents();

        $modified = $aggregate->isModified();

        $this->assertFalse($modified);
    }
}
