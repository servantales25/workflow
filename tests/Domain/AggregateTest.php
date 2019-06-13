<?php

namespace LuKun\Workflow\Tests\Domain;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Tests\Domain\Fakes\FakeAggregate;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent2;

class AggregateTest extends TestCase
{
    public function test_onChange()
    {
        $id = 1;
        $aggregate = new FakeAggregate($id);
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $onChangeArg = null;
        $aggregate->onChange(function ($event) use (&$onChangeArg) {
            $onChangeArg = $event;
        });

        $aggregate->submit($event1);

        $this->assertSame($event1, $onChangeArg);

        $aggregate->submit($event2);

        $this->assertSame($event2, $onChangeArg);
    }

    public function test_on()
    {
        $id = 1;
        $aggregate = new FakeAggregate($id);
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $onEvent1Arg = null;
        $aggregate->on(FakeEvent::class, function ($event) use (&$onEvent1Arg) {
            $onEvent1Arg = $event;
        });

        $onEvent2Arg = null;
        $aggregate->on(FakeEvent2::class, function ($event) use (&$onEvent2Arg) {
            $onEvent2Arg = $event;
        });

        $aggregate->submit($event1);

        $this->assertSame($event1, $onEvent1Arg);

        $aggregate->submit($event2);

        $this->assertSame($event2, $onEvent2Arg);
    }
}
