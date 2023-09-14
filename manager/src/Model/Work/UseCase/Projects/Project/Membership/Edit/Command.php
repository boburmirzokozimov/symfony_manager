<?php

namespace App\Model\Work\UseCase\Projects\Project\Membership\Edit;

use App\Model\Work\Entity\Projects\Project\Department\Department;
use App\Model\Work\Entity\Projects\Project\Membership;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Role\Role;

class Command
{
	public array $departments;

	public array $roles;

	public function __construct(public string $project, public string $memberId)
	{
	}

	public static function fromCommand(Project $project, Membership $membership): self
	{
		$command = new self($project->getId()->getValue(), $membership->getMember()->getId()->getValue());
		$command->departments = array_map(static function (Department $department): string {
			return $department->getId()->getValue();
		}, $membership->getDepartments());
		$command->roles = array_map(static function (Role $role): string {
			return $role->getId()->getValue();
		}, $membership->getRoles());

		return $command;
	}
}