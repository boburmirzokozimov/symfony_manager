<?php

namespace App\Model\Work\UseCase\Projects\Task\Plan\Set;

use App\Model\Work\Entity\Projects\Task\Task;

class Command
{
	public \DateTimeImmutable $plan;

	public function __construct(private int $id)
	{
	}

	public static function fromCommand(Task $task): self
	{
		$command = new self($task->getId()->getValue());
		$command->plan = $task->getPlanDate() ?: new \DateTimeImmutable();
		return $command;
	}

	public function getId(): int
	{
		return $this->id;
	}
}