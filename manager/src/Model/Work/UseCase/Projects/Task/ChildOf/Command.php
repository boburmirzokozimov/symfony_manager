<?php

namespace App\Model\Work\UseCase\Projects\Task\ChildOf;

use App\Model\Work\Entity\Projects\Task\Task;

class Command
{
	public ?int $parent;

	public function __construct(private int $id)
	{
	}

	public static function fromCommand(Task $task): self
	{
		$command = new self($task->getId()->getValue());
		$command->parent = $task->getParent()?->getId()->getValue();
		return $command;
	}

	public function getId(): int
	{
		return $this->id;
	}
}