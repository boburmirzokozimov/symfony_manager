<?php

namespace App\Model\Work\UseCase\Projects\Role\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Projects\Role\RoleRepository;

class Handler
{
	public function __construct(private RoleRepository    $repository,
								private ProjectRepository $projectRepository,
								private Flusher           $flusher)
	{
	}

	public function handle(Command $command)
	{
		if (!$role = $this->repository->get($command->getId())) {
			throw new \DomainException('Role was not found in repository');
		}

		if ($this->projectRepository->hasMembersWithRole($role->getId())) {
			throw new \DomainException('Role contains members');
		}

		$this->repository->remove($role);

		$this->flusher->flush();
	}
}