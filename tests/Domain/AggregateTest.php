<?php

namespace LuKun\Workflow\Tests\Domain;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Domain\IntId;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent2;
use LuKun\Workflow\Tests\Domain\Fakes\FakeAggregate;

class AggregateTest extends TestCase
{
    public function test_onChange()
    {
        $id = new IntId(1);
        $aggregate = new FakeAggregate($id);
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $onChangeArg = null;
        $aggregate->onChange(function (object $event) use (&$onChangeArg) {
            $onChangeArg = $event;
        });

        $aggregate->submit($event1);

        $this->assertSame($event1, $onChangeArg);

        $aggregate->submit($event2);

        $this->assertSame($event2, $onChangeArg);
    }
}
