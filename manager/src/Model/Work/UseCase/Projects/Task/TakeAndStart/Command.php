<?php

namespace App\Model\Work\UseCase\Projects\Task\TakeAndStart;

class Command
{
	public function __construct(private int $id, private string $actor)
	{
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