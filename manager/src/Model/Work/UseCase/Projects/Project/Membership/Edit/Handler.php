<?php

namespace App\Model\Work\UseCase\Projects\Project\Membership\Edit;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Projects\Role\Id as RoleId;
use App\Model\Work\Entity\Projects\Role\Role;
use App\Model\Work\Entity\Projects\Role\RoleRepository;

class Handler
{
	public function __construct(private RoleRepository    $roleRepository,
								private ProjectRepository $projectRepository,
								private Flusher           $flusher)
	{
	}

	public function handle(Command $command)
	{
		$project = $this->projectRepository->get(new Id($command->project));
		$member = new MemberId($command->memberId);

		$departments = array_map(function (string $id): DepartmentId {
			return new DepartmentId($id);
		}, $command->departments);

		$roles = array_map(function (string $id): Role {
			return $this->roleRepository->get(new RoleId($id));
		}, $command->roles);

		$project->editMember($member, $departments, $roles);

		$this->flusher->flush();
	}
}