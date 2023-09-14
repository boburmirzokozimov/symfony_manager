<?php

namespace App\Model\Work\UseCase\Projects\Task\Status;

use App\Model\Work\Entity\Projects\Task\Task;

class Command
{
	public string $status;

	public function __construct(private int $id)
	{
	}

	public static function fromCommand(Task $task): self
	{
		$command = new self($task->getId()->getValue());
		$command->status = $task->getStatus()->getValue();
		return $command;
	}

	public function getId(): int
	{
		return $this->id;
	}
}