<?php

namespace LuKun\Workflow\DI;

use LuKun\Structures\Collections\HashTable;
use LuKun\Workflow\Events\IEventHandlerLocator;

class LazyEventHandlerLocator implements IEventHandlerLocator
{
    /** @var IServiceLocator */
    private $serviceLocator;
    /** @var HashTable */
    private $handlers;

    public function __construct(IServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->handlers = new HashTable();
    }

    /** @return callable[] */
    public function findEventHandlersFor(string $event): array
    {
        $out = [];
        $this->handlers->walkOf($event, function (string $handlerClass) use (&$out) {
            $handler = $this->serviceLocator->findByType($handlerClass);
            if ($handler !== null) {
                array_push($out, $handler);
            }
        });

        return $out;
    }

    public function registerHandler(string $event, string $handlerClass): void
    {
        $this->handlers->addTo($event, $handlerClass);
    }
}
