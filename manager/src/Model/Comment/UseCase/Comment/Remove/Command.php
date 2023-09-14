<?php

namespace App\Model\Comment\UseCase\Comment\Remove;

class Command
{
    public function __construct(private string $comment)
    {
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}