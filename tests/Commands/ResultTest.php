<?php

namespace LuKun\Workflow\Tests\Commands;

use PHPUnit\Framework\TestCase;
use stdClass;
use LuKun\Workflow\Commands\Result;
use InvalidArgumentException;

class Error1
{ }
class Error2
{ }
class Event1
{ }
class Event2
{ }

class ResultTest extends TestCase
{
    public function test_WithErrors_WithoutEvents()
    {
        $error1 = new stdClass();
        $error2 = new stdClass();

        $readErrors = [];
        $readEvents = [];

        $result = Result::createFailure($error1, $error2);
        $isOk = $result->isOk();
        $isEmpty = $result->isEmpty();
        $result->readErrors(function ($error) use (&$readErrors) {
            array_push($readErrors, $error);
        });
        $result->readEvents(function ($event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $this->assertFalse($isOk);
        $this->assertFalse($isEmpty);
        $this->assertSame([$error1, $error2], $readErrors);
        $this->assertSame([], $readEvents);
    }

    public function test_WithoutErrors_WithEvents()
    {
        $event1 = new stdClass();
        $event2 = new stdClass();

        $readErrors = [];
        $readEvents = [];

        $result = Result::createSuccess($event1, $event2);
        $isOk = $result->isOk();
        $isEmpty = $result->isEmpty();
        $result->readErrors(function ($error) use (&$readErrors) {
            array_push($readErrors, $error);
        });
        $result->readEvents(function ($event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $this->assertTrue($isOk);
        $this->assertFalse($isEmpty);
        $this->assertSame([], $readErrors);
        $this->assertSame([$event1, $event2], $readEvents);
    }

    public function test_WithoutErrors_WithoutEvents()
    {
        $readErrors = [];
        $readEvents = [];

        $result = Result::createEmpty();
        $isOk = $result->isOk();
        $isEmpty = $result->isEmpty();
        $result->readErrors(function ($error) use (&$readErrors) {
            array_push($readErrors, $error);
        });
        $result->readEvents(function ($event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $this->assertTrue($isOk);
        $this->assertTrue($isEmpty);
        $this->assertSame([], $readErrors);
        $this->assertSame([], $readEvents);
    }

    public function test_WithErrors_WithEvents()
    {
        $error1 = new stdClass();
        $error2 = new stdClass();
        $event1 = new stdClass();
        $event2 = new stdClass();

        $readErrors = [];
        $readEvents = [];

        $result = new Result([$error1, $error2], [$event1, $event2]);
        $isOk = $result->isOk();
        $isEmpty = $result->isEmpty();
        $result->readErrors(function ($error) use (&$readErrors) {
            array_push($readErrors, $error);
        });
        $result->readEvents(function ($event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $this->assertFalse($isOk);
        $this->assertFalse($isEmpty);
        $this->assertSame([$error1, $error2], $readErrors);
        $this->assertSame([$event1, $event2], $readEvents);
    }

    public function test_readErrorsOf()
    {
        $error1 = new Error1();
        $error2 = new Error2();

        $readErrorsOfError1 = [];
        $readErrorsOfError2 = [];

        $result = Result::createFailure($error1, $error2);
        $result->readErrorsOf(Error1::class, function ($error) use (&$readErrorsOfError1) {
            array_push($readErrorsOfError1, $error);
        });
        $result->readErrorsOf(Error2::class, function ($error) use (&$readErrorsOfError2) {
            array_push($readErrorsOfError2, $error);
        });

        $this->assertSame([$error1], $readErrorsOfError1);
        $this->assertSame([$error2], $readErrorsOfError2);
    }

    public function test_readEventsOf()
    {
        $event1 = new Event1();
        $event2 = new Event2();

        $readEventsOfEvent1 = [];
        $readEventsOfEvent2 = [];

        $result = Result::createSuccess($event1, $event2);
        $result->readEventsOf(Event1::class, function ($error) use (&$readEventsOfEvent1) {
            array_push($readEventsOfEvent1, $error);
        });
        $result->readEventsOf(Event2::class, function ($error) use (&$readEventsOfEvent2) {
            array_push($readEventsOfEvent2, $error);
        });

        $this->assertSame([$event1], $readEventsOfEvent1);
        $this->assertSame([$event2], $readEventsOfEvent2);
    }

    public function test_createFailure_WithoutErrors()
    {
        $this->expectException(InvalidArgumentException::class);
        Result::createFailure();
    }
}
