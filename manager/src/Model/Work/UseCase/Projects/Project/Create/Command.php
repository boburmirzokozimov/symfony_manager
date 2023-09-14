<?php

namespace App\Model\Work\UseCase\Projects\Project\Create;

use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
	#[NotBlank]
	public string $name;
	#[NotBlank]
	public int $sort;

	public function getName(): string
	{
		return $this->name;
	}

	public function getSort(): int
	{
		return $this->sort;
	}
}