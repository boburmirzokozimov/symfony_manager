<?php

namespace App\ReadModel\Work\Projects\Calendar;

use DateTimeImmutable;

class Result
{

    public function __construct(public readonly array $items, public readonly DateTimeImmutable $start, public readonly DateTimeImmutable $end, public readonly DateTimeImmutable $month)
    {
    }

    public function getItem(): array
    {
        return $this->items;
    }

    public function getStart(): DateTimeImmutable
    {
        return $this->start;
    }

    public function getEnd(): DateTimeImmutable
    {
        return $this->end;
    }

    public function getMonth(): DateTimeImmutable
    {
        return $this->month;
    }


}