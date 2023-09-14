<?php

namespace App\Model\Comment\UseCase\Comment\Edit;

use App\Model\Comment\Entity\Comment\Comment;
use App\Model\Work\Entity\Projects\Task\Task;

class Command
{
    public string $text;

    public function __construct(private int $task, private string $comment)
    {
    }

    public static function fromCommand(Task $task, Comment $comment): self
    {
        $command = new self($task->getId()->getValue(), $comment->getId()->getValue());
        $command->text = $comment->getText();
        return $command;
    }

    public function getTask(): int
    {
        return $this->task;
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}