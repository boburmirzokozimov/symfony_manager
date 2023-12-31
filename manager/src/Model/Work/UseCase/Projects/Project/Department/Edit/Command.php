<?php

namespace App\Model\Work\UseCase\Projects\Project\Department\Edit;

use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
	#[NotBlank]
	public string $project;

	#[NotBlank]
	public string $id;

	#[NotBlank]
	public string $name;

	public function __construct(string $project, string $id)
	{
		$this->project = $project;
		$this->id = $id;
	}

}