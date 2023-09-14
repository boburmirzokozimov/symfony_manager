<?php

namespace App\Model\Work\UseCase\Projects\Task\Move;

use App\Model\Work\Entity\Projects\Task\Task;

class Command
{
	public string $project;

	public bool $withChildren;

	public function __construct(private int $id)
	{
	}

	public static function fromCommand(Task $task): self
	{
		$command = new self($task->getId()->getValue());
		$command->project = $task->getProject()->getId()->getValue();
		return $command;
	}

	public function getId(): int
	{
		return $this->id;
	}
}