<?php

namespace LuKun\Workflow\Commands;

class ResultBuilder
{
    /** @var object[] */
    private $errors;
    /** @var object[] */
    private $events;

    public function __construct()
    {
        $this->errors = [];
        $this->events = [];
    }

    public function addError(object $error): void
    {
        array_push($this->errors, $error);
    }

    public function addEvent(object $event): void
    {
        array_push($this->events, $event);
    }

    public function addResult(Result $result): void
    {
        $result->readErrors([$this, 'addError']);
        $result->readEvents([$this, 'addEvent']);
    }

    public function getResult(): Result
    {
        return new Result($this->errors, $this->events);
    }
}
