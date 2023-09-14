<?php

namespace App\Model\Work\UseCase\Projects\Task\Executor\Assign;

use App\Model\Work\Entity\Projects\Task\Task;

class Command
{
	public array $members;

	public function __construct(private int $id)
	{
	}

	public static function fromCommand(Task $task): self
	{
		$command = new self($task->getId()->getValue());
		$command->members = $task->executors->toArray();
		return $command;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getActor(): string
	{
		return $this->actor;
	}
}