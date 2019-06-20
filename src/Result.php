<?php

namespace LuKun\Workflow;

use LuKun\Structures\Collections\HashTable;
use LuKun\Structures\Collections\Vector;
use Throwable;

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

    private function __construct()
    {
        $this->errors = new HashTable();
        $this->errorsIndex = new Vector();
        $this->events = new HashTable();
        $this->eventsIndex = new Vector();
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

    public function countErrors(): int
    {
        return $this->errors->getLength();
    }

    public function countEvents(): int
    {
        return $this->events->getLength();
    }

    public function countErrorsOf(string $error): int
    {
        return $this->errors->getLengthOf($error);
    }

    public function countEventsOf(string $event): int
    {
        return $this->events->getLengthOf($event);
    }

    public function getError(string $error, int $offset = 0): ?Throwable
    {
        return $this->errors->get($error, $offset);
    }

    public function getEvent(string $event, int $offset = 0): ?object
    {
        return $this->events->get($event, $offset);
    }

    /** @param callable $handleError - (Throwable $error): void */
    public function readErrors(callable $handleError): void
    {
        $this->errorsIndex->walk(function (array $hash_index) use ($handleError) {
            [$hash, $index] = $hash_index;
            $error = $this->errors->get($hash, $index);
            $handleError($error);
        });
    }

    /** @param callable $handleError - (Throwable $error): void */
    public function readErrorsOf(string $error, callable $handleError): void
    {
        $this->errors->walkOf($error, $handleError);
    }

    /** @param callable $handleEvent - (object $event): void */
    public function readEvents(callable $handleEvent): void
    {
        $this->eventsIndex->walk(function (array $hash_index) use ($handleEvent) {
            [$hash, $index] = $hash_index;
            $event = $this->events->get($hash, $index);
            $handleEvent($event);
        });
    }

    /** @param callable $handleEvent - (object $event): void */
    public function readEventsOf(string $event, callable $handleEvent): void
    {
        $this->events->walkOf($event, $handleEvent);
    }

    private function _addError(Throwable $error): void
    {
        $hash = get_class($error);
        $index = $this->errors->addTo($hash, $error);
        $this->errorsIndex->add([$hash, $index]);
    }

    private function _addEvent(object $event): void
    {
        $hash = get_class($event);
        $index = $this->events->addTo($hash, $event);
        $this->eventsIndex->add([$hash, $index]);
    }

    private function _addResult(Result $result): void
    {
        $result->readErrors(function (object $error) {
            $this->_addError($error);
        });
        $result->readEvents(function (object $event) {
            $this->_addEvent($event);
        });
    }

    public static function createEmpty(): Result
    {
        return new Result([], []);
    }

    public static function createFailure(?Throwable ...$errors): Result
    {
        $result = new Result();
        foreach ($errors as $error) {
            if ($error !== null) {
                $result->_addError($error);
            }
        }

        return $result;
    }

    public static function createSuccess(?object ...$events): Result
    {
        $result = new Result();
        foreach ($events as $event) {
            if ($event !== null) {
                $result->_addEvent($event);
            }
        }

        return $result;
    }

    public static function createMerge(Result ...$results): Result
    {
        $_result = new Result();
        foreach ($results as $result) {
            $_result->_addResult($result);
        }

        return $_result;
    }
}
