<?php

namespace App\Model\Work\UseCase\Projects\Project\Department\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
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
		if (!$project = $this->repository->get(new Id($command->project))) {
			throw new \DomainException('Unable to find project');
		}

		$project->addDepartment(
			DepartmentId::next(),
			$command->name
		);

		$this->flusher->flush();
	}
}