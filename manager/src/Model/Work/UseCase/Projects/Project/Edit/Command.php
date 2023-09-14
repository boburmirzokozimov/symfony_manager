<?php

namespace App\Model\Work\UseCase\Projects\Project\Edit;

use App\Model\Work\Entity\Projects\Project\Project;
use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
	#[NotBlank]
	private string $name;

	#[NotBlank]
	private int $sort;

	public function __construct(private string $id)
	{
	}

	public static function fromProject(Project $project): self
	{
		$command = new self($project->getId()->getValue());
		$command->setName($project->getName());
		$command->setSort($project->getSort());
		return $command;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getSort(): int
	{
		return $this->sort;
	}

	public function setSort(int $sort): void
	{
		$this->sort = $sort;
	}
}