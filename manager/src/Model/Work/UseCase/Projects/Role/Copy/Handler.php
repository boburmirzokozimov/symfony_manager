<?php

namespace App\Model\Work\UseCase\Projects\Role\Copy;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Role\Id;
use App\Model\Work\Entity\Projects\Role\RoleRepository;
use DomainException;

class Handler
{
	public function __construct(private RoleRepository $repository, private Flusher $flusher)
	{
	}

	public function handle(Command $command)
	{
		$role = $this->repository->get($command->getId());

		if ($this->repository->hasByName($command->getName())) {
			throw new DomainException('Role with this name already exists.');
		}

		$new = $role->clone(
			Id::next(),
			$command->getName()
		);

		$this->repository->add($new);
		
		$this->flusher->flush();

	}
}