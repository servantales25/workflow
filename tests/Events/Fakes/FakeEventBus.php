<?php

namespace LuKun\Workflow\Tests\Events\Fakes;

use LuKun\Workflow\Events\IEventBus;

class FakeEventBus implements IEventBus
{
    /** @var callable - (object $event): void */
    public $onPublish;

    public function __construct()
    {
        $this->onPublish = function ($event) { };
    }

    public function publish(object $event): void
    {
        call_user_func($this->onPublish, $event);
    }
}
