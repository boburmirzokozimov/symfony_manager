<?php

namespace App\Model\Work\UseCase\Members\Group\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Group\GroupRepository;
use App\Model\Work\Entity\Members\Group\Id;

class Handler
{
	public function __construct(private GroupRepository $repository,
								private Flusher         $flusher)
	{
	}

	public function handle(Command $command)
	{
		if ($this->repository->hasByName($command->name)) {
			throw new \DomainException('Group with this name already exists');
		}

		$group = new Group(
			Id::next(),
			$command->name);

		$this->repository->add($group);

		$this->flusher->flush();
	}
}