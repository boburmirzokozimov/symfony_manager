<?php

namespace App\Model;

trait EventsTrait
{
    private array $recordedEvents = [];

    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];
        return $events;
    }

    protected function recordEvent(object $event): void
    {
        $this->recordedEvents[] = $event;
    }
}