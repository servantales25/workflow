<?php

namespace LuKun\Workflow\Tests\Events;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Tests\Events\Fakes\FakeEventBus;
use LuKun\Workflow\Commands\Result;
use stdClass;
use LuKun\Workflow\Events\EventPublishingCommandBusGateway;

class EventPublishingCommandBusGatewayTest extends TestCase
{
    /** @var FakeEventBus */
    private $eventBus;
    /** @var EventPublishingCommandBusGateway */
    private $gateway;

    protected function setUp(): void
    {
        $this->eventBus = new FakeEventBus();
        $this->gateway = new EventPublishingCommandBusGateway($this->eventBus);
    }

    public function test_commandCompleted_WithSuccessfulResult()
    {
        $command = new stdClass();
        $event1 = new stdClass();
        $event2 = new stdClass();
        $result = Result::createSuccess($event1, $event2);

        $publishedEvents = [];
        $this->eventBus->onPublish = function ($event) use (&$publishedEvents) {
            array_push($publishedEvents, $event);
        };

        $this->gateway->commandCompleted($command, $result);

        $this->assertSame([$event1, $event2], $publishedEvents);
    }

    public function test_commandCompleted_WithFailedResult()
    {
        $command = new stdClass();
        $error = new stdClass();
        $event1 = new stdClass();
        $event2 = new stdClass();
        $result = new Result([$error], [$event1, $event2]);

        $publishedEvents = [];
        $this->eventBus->onPublish = function ($event) use (&$publishedEvents) {
            array_push($publishedEvents, $event);
        };

        $this->gateway->commandCompleted($command, $result);

        $this->assertSame([$event1, $event2], $publishedEvents);
    }

    public function test_commandCompleted_WithEmptyResult()
    {
        $command = new stdClass();
        $result = Result::createEmpty();

        $publishedEvents = [];
        $this->eventBus->onPublish = function ($event) use (&$publishedEvents) {
            array_push($publishedEvents, $event);
        };

        $this->gateway->commandCompleted($command, $result);

        $this->assertSame([], $publishedEvents);
    }

    public function test_commandReceived_transparentToArgumentsPipeline()
    {
        $command = new stdClass();

        $returnedCommand = $this->gateway->commandReceived($command);

        $this->assertSame($command, $returnedCommand);
    }

    public function test_commandCompleted_transparentToArgumentsPipeline()
    {
        $command = new stdClass();
        $result = Result::createEmpty();

        $returnedResult = $this->gateway->commandCompleted($command, $result);

        $this->assertSame($result, $returnedResult);
    }
}
