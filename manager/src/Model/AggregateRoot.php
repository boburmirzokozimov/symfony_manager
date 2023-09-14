<?php

namespace App\Model;

interface AggregateRoot
{
    public function releaseEvents(): array;
}