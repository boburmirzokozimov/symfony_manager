<?php

namespace App\Model\Work\UseCase\Projects\Task\Priority;

use App\Model\Work\Entity\Projects\Task\Task;

class Command
{
	public int $priority;

	public function __construct(private int $id)
	{
	}

	public static function fromCommand(Task $task): self
	{
		$command = new self($task->getId()->getValue());
		$command->priority = $task->getPriority();
		return $command;
	}

	public function getId(): int
	{
		return $this->id;
	}
}