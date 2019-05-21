<?php

namespace LuKun\Workflow\Commands;

use LuKun\Structures\Collections\HashTable;
use LuKun\Structures\Collections\Vector;
use InvalidArgumentException;

class Result
{
    /** @var HashTable */
    private $errors;
    /** @var Vector */
    private $errorsIndex;
    /** @var HashTable */
    private $events;
    /** @var Vector */
    private $eventsIndex;

    /**
     * @param object[] $errors
     * @param object[] $events
     */
    public function __construct(array $errors, array $events)
    {
        $this->_initErrors($errors);
        $this->_initEvents($events);
    }

    public function isOk(): bool
    {
        return $this->errors->isEmpty();
    }

    public function isEmpty(): bool
    {
        return $this->errors->isEmpty() && $this->events->isEmpty();
    }

    public function containsErrorOf(string $error): bool
    {
        return $this->errors->containsAnyOf($error);
    }

    public function containsEventOf(string $event): bool
    {
        return $this->events->containsAnyOf($event);
    }

    public function readErrors(callable $handleError): void
    {
        $this->errorsIndex->walk(function (array $hash_index) use ($handleError) {
            [$hash, $index] = $hash_index;
            $error = $this->errors->get($hash, $index);
            $handleError($error);
        });
    }

    public function readErrorsOf(string $error, callable $handleError): void
    {
        $this->errors->walkOf($error, $handleError);
    }

    public function readEvents(callable $handleEvent): void
    {
        $this->eventsIndex->walk(function (array $hash_index) use ($handleEvent) {
            [$hash, $index] = $hash_index;
            $event = $this->events->get($hash, $index);
            $handleEvent($event);
        });
    }

    public function readEventsOf(string $event, callable $handleEvent): void
    {
        $this->events->walkOf($event, $handleEvent);
    }

    /** @param object[] $errors */
    private function _initErrors(array $errors): void
    {
        $this->errors = new HashTable();
        $this->errorsIndex = new Vector();
        foreach ($errors as $error) {
            $hash = get_class($error);
            $index = $this->errors->addTo($hash, $error);
            $this->errorsIndex->add([$hash, $index]);
        }
    }

    /** @param object[] $events */
    private function _initEvents(array $events): void
    {
        $this->events = new HashTable();
        $this->eventsIndex = new Vector();
        foreach ($events as $event) {
            $hash = get_class($event);
            $index = $this->events->addTo($hash, $event);
            $this->eventsIndex->add([$hash, $index]);
        }
    }

    public static function createEmpty(): Result
    {
        return new Result([], []);
    }

    public static function createFailure(object ...$errors): Result
    {
        if (count($errors) < 1) {
            throw new InvalidArgumentException('There must be at least one error object when creating failure result.');
        }

        return new Result($errors, []);
    }

    public static function createSuccess(object ...$events): Result
    {
        return new Result([], $events);
    }
}
