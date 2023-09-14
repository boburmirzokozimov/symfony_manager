<?php

namespace App\Model\Work\UseCase\Projects\Project\Reinstate;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;

class Handler
{
	public function __construct(private ProjectRepository $repository,
								private Flusher           $flusher)
	{
	}

	public function handle(Command $command)
	{
		if (!$project = $this->repository->get(new Id($command->getId()))) {
			throw new \DomainException('Project does not exist');
		}

		$project->reinstate();

		$this->flusher->flush();
	}
}