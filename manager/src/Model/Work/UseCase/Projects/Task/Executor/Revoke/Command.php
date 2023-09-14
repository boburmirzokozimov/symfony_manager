<?php

namespace App\Model\Work\UseCase\Projects\Task\Executor\Revoke;

class Command
{
	public function __construct(private int $id, private string $member)
	{
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getMember(): string
	{
		return $this->member;
	}
}