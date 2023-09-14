<?php

namespace App\Model\Work\UseCase\Projects\Task\Files\Add;

class Command
{
    public int $id;

    public string $member;

    /**
     * @var File[]
     */
    public $files;

    public function __construct(int $id, string $member)
    {
        $this->id = $id;
        $this->member = $member;
    }
}