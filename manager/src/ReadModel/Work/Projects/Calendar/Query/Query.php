<?php

namespace App\ReadModel\Work\Projects\Calendar\Query;

use DateTimeImmutable;

class Query
{
    public ?string $project = null;
    public ?string $member = null;

    public function __construct(public int $year, public int $month)
    {
    }

    public static function fromDate(DateTimeImmutable $date): self
    {
        return new self((int)$date->format('Y'), (int)$date->format('m'));
    }

    public function forMember(string $member): self
    {
        $clone = clone $this;
        $clone->member = $member;
        return $clone;
    }

    public function forProject(string $project): self
    {
        $clone = clone $this;
        $clone->project = $project;
        return $clone;
    }
}