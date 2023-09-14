<?php

namespace App\Model\Work\UseCase\Projects\Task\Type;

use App\Model\Work\Entity\Projects\Task\Task;

class Command
{
	public string $type;

	public function __construct(private int $id)
	{
	}

	public static function fromCommand(Task $task): self
	{
		$command = new self($task->getId()->getValue());
		$command->type = $task->getType();
		return $command;
	}

	public function getId(): int
	{
		return $this->id;
	}
}