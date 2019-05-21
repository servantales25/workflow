<?php

namespace LuKun\Workflow\Tests\Commands;

use PHPUnit\Framework\TestCase;
use stdClass;
use LuKun\Workflow\Commands\ResultBuilder;
use LuKun\Workflow\Commands\Result;

class ResultBuilderTest extends TestCase
{
    /** @var ResultBuilder */
    private $builder;

    protected function setUp(): void
    {
        $this->builder = new ResultBuilder();
    }

    public function test_addError()
    {
        $error1 = new stdClass();
        $error2 = new stdClass();

        $readErrors = [];

        $this->builder->addError($error1);
        $this->builder->addError($error2);

        $result = $this->builder->getResult();
        $result->readErrors(function ($error) use (&$readErrors) {
            array_push($readErrors, $error);
        });

        $this->assertSame([$error1, $error2], $readErrors);
    }

    public function test_addEvent()
    {
        $event1 = new stdClass();
        $event2 = new stdClass();

        $readEvents = [];

        $this->builder->addEvent($event1);
        $this->builder->addEvent($event2);

        $result = $this->builder->getResult();
        $result->readEvents(function ($event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $this->assertSame([$event1, $event2], $readEvents);
    }

    public function test_addResult()
    {
        $error1 = new stdClass();
        $error2 = new stdClass();
        $error3 = new stdClass();
        $error4 = new stdClass();
        $event1 = new stdClass();
        $event2 = new stdClass();
        $event3 = new stdClass();
        $event4 = new stdClass();
        $addedResult = new Result([$error3, $error4], [$event3, $event4]);

        $readErrors = [];
        $readEvents = [];

        $this->builder->addError($error1);
        $this->builder->addError($error2);
        $this->builder->addEvent($event1);
        $this->builder->addEvent($event2);
        $this->builder->addResult($addedResult);

        $result = $this->builder->getResult();
        $result->readErrors(function ($error) use (&$readErrors) {
            array_push($readErrors, $error);
        });
        $result->readEvents(function ($event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $this->assertSame([$error1, $error2, $error3, $error4], $readErrors);
        $this->assertSame([$event1, $event2, $event3, $event4], $readEvents);
    }

    public function test_getResult_Empty()
    {
        $result = $this->builder->getResult();
        $isEmpty = $result->isEmpty();

        $this->assertTrue($isEmpty);
    }
}
