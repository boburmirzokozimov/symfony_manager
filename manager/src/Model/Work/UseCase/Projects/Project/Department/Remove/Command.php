<?php

namespace App\Model\Work\UseCase\Projects\Project\Department\Remove;

class Command
{
	public string $id;

	public string $project;

	public function __construct(string $project, string $id)
	{
		$this->id = $id;
		$this->project = $project;
	}
}