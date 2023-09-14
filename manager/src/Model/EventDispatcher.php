<?php

namespace App\Model;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatcher
{
    public function __construct(private EventDispatcherInterface $dispatcher)
    {
    }

    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}