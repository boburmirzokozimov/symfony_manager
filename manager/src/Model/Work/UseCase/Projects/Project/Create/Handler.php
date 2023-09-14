<?php

namespace App\Model\Work\UseCase\Projects\Project\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;

class Handler
{
	public function __construct(private ProjectRepository $repository,
								private Flusher           $flusher)
	{
	}

	public function handle(Command $command)
	{
		if (!$this->repository->hasByName($command->getName())) {
			throw new \DomainException('Project with this name already exists');
		}
		$project = new Project(
			Id::next(),
			$command->getName(),
			$command->getSort()
		);

		$this->repository->add($project);

		$this->flusher->flush();
	}
}