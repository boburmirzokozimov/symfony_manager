<?php

namespace App\Model\Work\UseCase\Projects\Task\Edit;

use App\Model\Work\Entity\Projects\Task\Task;
use JetBrains\PhpStorm\Pure;

class Command
{
	public string $name;
	public string $content;

	public function __construct(private int $taskId)
	{
	}

	#[Pure] public static function fromCommand(Task $task): self
	{
		$command = new self($task->getId()->getValue());
		$command->name = $task->getName();
		$command->content = $task->getContent();
		return $command;
	}

	public function getTaskId(): int
	{
		return $this->taskId;
	}
}