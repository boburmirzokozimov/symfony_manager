<?php

namespace App\Model\Work\UseCase\Projects\Task\Progress;

use App\Model\Work\Entity\Projects\Task\Task;
use JetBrains\PhpStorm\Pure;

class Command
{
	public int $progress;

	public function __construct(private int $id)
	{
	}

	#[Pure] public static function fromCommand(Task $task): self
	{
		$command = new self($task->getId()->getValue());
		$command->progress = $task->getProgress();
		return $command;
	}

	public function getId(): int
	{
		return $this->id;
	}
}