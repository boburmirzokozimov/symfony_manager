<?php

namespace App\Model\Work\UseCase\Projects\Project\Membership\Add;

class Command
{
	public string $member;

	public array $departments;

	public array $roles;

	public function __construct(public string $project)
	{
	}
}