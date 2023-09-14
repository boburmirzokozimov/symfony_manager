<?php

namespace App\Model\Work\UseCase\Projects\Role\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Role\Id;
use App\Model\Work\Entity\Projects\Role\Role;
use App\Model\Work\Entity\Projects\Role\RoleRepository;

class Handler
{
	public function __construct(private RoleRepository $repository,
								private Flusher        $flusher)
	{
	}

	public function handle(Command $command)
	{
		if ($this->repository->hasByName($command->getName())) {
			throw new \DomainException('This kind of name exists in the repository');
		}
		$role = new Role(
			Id::next(),
			$command->getName(),
			$command->getPermissions()
		);

		$this->repository->add($role);

		$this->flusher->flush();
	}
}