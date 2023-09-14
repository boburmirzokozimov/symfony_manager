<?php

namespace App\Model\Work\UseCase\Members\Group\Edit;

use App\Model\Flusher;
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
		if (!$group = $this->repository->get(new Id($command->id))) {
			throw new \DomainException('Group not found');
		};

		$group->edit($command->name);

		$this->flusher->flush();
	}
}