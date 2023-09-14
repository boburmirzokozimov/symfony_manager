<?php

namespace App\Model\Work\UseCase\Members\Member\Reinstate;

class Command
{
	public function __construct(private string $id)
	{
	}

	public function getId(): string
	{
		return $this->id;
	}
}