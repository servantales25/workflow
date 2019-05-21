<?php

namespace LuKun\Workflow\Tests\Events\Fakes;

use LuKun\Workflow\Events\EventEmitter;

class FakeEventEmitter extends EventEmitter
{
    public function __construct()
    {
        parent::__construct();
    }

    public function emit(object $event): void
    {
        parent::emit($event);
    }
}
