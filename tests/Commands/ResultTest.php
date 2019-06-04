<?php

namespace LuKun\Workflow\Tests\Commands;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Commands\Result;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent;
use LuKun\Workflow\Tests\Events\Fakes\FakeEvent2;
use Exception;
use RuntimeException;
use Throwable;

class ResultTest extends TestCase
{
    public function test_createFailure()
    {
        $error1 = new Exception();
        $error2 = new Exception();

        $readErrors = [];
        $readEvents = [];

        $result = Result::createFailure($error1, $error2);
        $isOk = $result->isOk();
        $isEmpty = $result->isEmpty();
        $result->readErrors(function (Throwable $error) use (&$readErrors) {
            array_push($readErrors, $error);
        });
        $result->readEvents(function (object $event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $this->assertFalse($isOk);
        $this->assertFalse($isEmpty);
        $this->assertSame([$error1, $error2], $readErrors);
        $this->assertSame([], $readEvents);
    }

    public function test_countErrors()
    {
        $error1 = new Exception();
        $error2 = new Exception();

        $result = Result::createFailure($error1, $error2);

        $this->assertSame(2, $result->countErrors());
        $this->assertSame(2, $result->countErrorsOf(Exception::class));
        $this->assertSame(0, $result->countErrorsOf(RuntimeException::class));
    }

    public function test_countEvents()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent();

        $result = Result::createSuccess($event1, $event2);

        $this->assertSame(2, $result->countEvents());
        $this->assertSame(2, $result->countEventsOf(FakeEvent::class));
        $this->assertSame(0, $result->countEventsOf(FakeEvent2::class));
    }

    public function getError()
    {
        $error1 = new Exception();
        $error2 = new RuntimeException();
        $error3 = new Exception();
        $error4 = new Exception();

        $result = Result::createFailure($error1, $error2, $error3, $error4);

        $this->assertSame($error1, $result->getError(Exception::class, 1));
        $this->assertSame($error3, $result->getError(Exception::class, 2));
        $this->assertSame($error4, $result->getError(Exception::class, 3));
        $this->assertSame($error2, $result->getError(RuntimeException::class, 1));
        $this->assertNull($result->getError(RuntimeException::class, 2));
    }

    public function getEvent()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();
        $event3 = new FakeEvent();
        $event4 = new FakeEvent();

        $result = Result::createFailure($event1, $event2, $event3, $event4);

        $this->assertSame($event1, $result->getEvent(FakeEvent::class, 1));
        $this->assertSame($event3, $result->getEvent(FakeEvent::class, 2));
        $this->assertSame($event4, $result->getEvent(FakeEvent::class, 3));
        $this->assertSame($event2, $result->getEvent(FakeEvent2::class, 1));
        $this->assertNull($result->getEvent(FakeEvent2::class, 2));
    }

    public function test_createSuccess()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $readErrors = [];
        $readEvents = [];

        $result = Result::createSuccess($event1, $event2);
        $isOk = $result->isOk();
        $isEmpty = $result->isEmpty();
        $result->readErrors(function (Throwable $error) use (&$readErrors) {
            array_push($readErrors, $error);
        });
        $result->readEvents(function (object $event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $this->assertTrue($isOk);
        $this->assertFalse($isEmpty);
        $this->assertSame([], $readErrors);
        $this->assertSame([$event1, $event2], $readEvents);
    }

    public function test_createEmpty()
    {
        $readErrors = [];
        $readEvents = [];

        $result = Result::createEmpty();
        $isOk = $result->isOk();
        $isEmpty = $result->isEmpty();
        $result->readErrors(function (Throwable $error) use (&$readErrors) {
            array_push($readErrors, $error);
        });
        $result->readEvents(function (object $event) use (&$readEvents) {
            array_push($readEvents, $event);
        });

        $this->assertTrue($isOk);
        $this->assertTrue($isEmpty);
        $this->assertSame([], $readErrors);
        $this->assertSame([], $readEvents);
    }

    // public function test_WithErrors_WithEvents()
    // {
    //     $error1 = new stdClass();
    //     $error2 = new stdClass();
    //     $event1 = new stdClass();
    //     $event2 = new stdClass();

    //     $readErrors = [];
    //     $readEvents = [];

    //     $result = new Result([$error1, $error2], [$event1, $event2]);
    //     $isOk = $result->isOk();
    //     $isEmpty = $result->isEmpty();
    //     $result->readErrors(function ($error) use (&$readErrors) {
    //         array_push($readErrors, $error);
    //     });
    //     $result->readEvents(function ($event) use (&$readEvents) {
    //         array_push($readEvents, $event);
    //     });

    //     $this->assertFalse($isOk);
    //     $this->assertFalse($isEmpty);
    //     $this->assertSame([$error1, $error2], $readErrors);
    //     $this->assertSame([$event1, $event2], $readEvents);
    // }

    public function test_readErrorsOf()
    {
        $error1 = new Exception();
        $error2 = new RuntimeException();

        $readErrorsOfError1 = [];
        $readErrorsOfError2 = [];

        $result = Result::createFailure($error1, $error2);
        $result->readErrorsOf(Exception::class, function (Throwable $error) use (&$readErrorsOfError1) {
            array_push($readErrorsOfError1, $error);
        });
        $result->readErrorsOf(RuntimeException::class, function (Throwable $error) use (&$readErrorsOfError2) {
            array_push($readErrorsOfError2, $error);
        });

        $this->assertSame([$error1], $readErrorsOfError1);
        $this->assertSame([$error2], $readErrorsOfError2);
    }

    public function test_readEventsOf()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();

        $readEventsOfEvent1 = [];
        $readEventsOfEvent2 = [];

        $result = Result::createSuccess($event1, $event2);
        $result->readEventsOf(FakeEvent::class, function (object $event) use (&$readEventsOfEvent1) {
            array_push($readEventsOfEvent1, $event);
        });
        $result->readEventsOf(FakeEvent2::class, function (object $event) use (&$readEventsOfEvent2) {
            array_push($readEventsOfEvent2, $event);
        });

        $this->assertSame([$event1], $readEventsOfEvent1);
        $this->assertSame([$event2], $readEventsOfEvent2);
    }

    public function test_createMerge()
    {
        $event1 = new FakeEvent();
        $event2 = new FakeEvent2();
        $error1 = new Exception();
        $error2 = new RuntimeException();

        $result1 = Result::createSuccess($event1, $event2);
        $result2 = Result::createFailure($error1, $error2);
        $result3 = Result::createSuccess($event1, $event2);
        $result4 = Result::createFailure($error1, $error2);

        $result = Result::createMerge($result1, $result2, $result3, $result4);

        $readEvents = [];
        $readErrors = [];

        $result->readEvents(function (object $event) use (&$readEvents) {
            array_push($readEvents, $event);
        });
        $result->readErrors(function (Throwable $error) use (&$readErrors) {
            array_push($readErrors, $error);
        });

        $this->assertSame([$event1, $event2, $event1, $event2], $readEvents);
        $this->assertSame([$error1, $error2, $error1, $error2], $readErrors);
    }
}
