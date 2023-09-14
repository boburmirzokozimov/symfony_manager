<?php

namespace App\Model\Work\UseCase\Projects\Role\Edit;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Role\RoleRepository;

class Handler
{
	public function __construct(private RoleRepository $repository,
								private Flusher        $flusher)
	{
	}

	public function handle(Command $command)
	{
		$role = $this->repository->get($command->getId());

		if ($this->repository->hasByName($command->name)) {
			throw new \DomainException('Role with this name already exists');
		}

		$role->edit($command->name, $command->permission);

		$this->flusher->flush();
	}
}