<?php

namespace App\Model\Work\UseCase\Projects\Task\Remove;

class Command
{
	public function __construct(private int $id)
	{
	}

	public function getId(): int
	{
		return $this->id;
	}
}