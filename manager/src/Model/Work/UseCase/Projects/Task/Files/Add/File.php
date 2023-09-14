<?php

namespace App\Model\Work\UseCase\Projects\Task\Files\Add;

class File
{
    public function __construct(public string $name, public string $path, public string $size)
    {
    }
}