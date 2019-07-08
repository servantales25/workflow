<?php

namespace LuKun\Workflow;

use LuKun\Events\EventEmitter;
use Throwable;

class EventBus extends EventEmitter
{
    /** (object $event): void */
    public const EVENT_RECEIVED = 'eventReceived';
    /** (object $event, int $listenerIndex, Throwable $exception): void */
    public const EVENT_HANDLING_FAILED = 'eventPublishingFailed';
    /** (object $event, int $listenerIndex): void */
    public const EVENT_PUBLISHED = 'eventPublished';

    /** @var IEventListenersLocator */
    private $listenersLocator;

    public function __construct(IEventListenersLocator $listenersLocator)
    {
        parent::__construct();

        $this->listenersLocator = $listenersLocator;
    }

    public function publish(object $event): void
    {
        $this->submit(self::EVENT_RECEIVED, $event);

        $eventClass = get_class($event);
        /** @var callable[] $listeners */
        $listeners = $this->listenersLocator->findEventListenersFor($eventClass);
        for ($i = 0; $i < count($listeners); ++$i) {
            $listener = $listeners[$i];
            try {
                call_user_func($listener, $event);
            } catch (Throwable $e) {
                $this->submit(self::EVENT_HANDLING_FAILED, $event, $i, $e);

                continue;
            }

            $this->submit(self::EVENT_PUBLISHED, $event, $i);
        }
    }
}
