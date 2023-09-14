<?php

namespace App\Model\Work\UseCase\Projects\Task\Files\Remove;

class Command
{

    public function __construct(public int $task, public string $id)
    {
    }
}