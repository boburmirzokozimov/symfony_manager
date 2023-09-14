<?php

namespace App\Model\Work\UseCase\Projects\Project\Archive;

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