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

        $onChangeArg1 = null;
        $onChangeArg2 = null;

        $aggregate->onChange(function ($arggregate, $event) use (&$onChangeArg1, &$onChangeArg2) {
            $onChangeArg1 = $arggregate;
            $onChangeArg2 = $event;
        });

        $aggregate->publishChange($event1);

        $this->assertSame($aggregate, $onChangeArg1);
        $this->assertSame($event1, $onChangeArg2);

        $aggregate->publishChange($event2);

        $this->assertSame($event2, $onChangeArg2);
    }
}
