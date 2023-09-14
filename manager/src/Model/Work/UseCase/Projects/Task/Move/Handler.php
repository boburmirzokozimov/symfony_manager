<?php

namespace App\Model\Work\UseCase\Projects\Task\Move;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Project\Id as ProjectId;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\TaskRepository;

class Handler
{
	public function __construct(
		private ProjectRepository $repository,
		private TaskRepository    $taskRepository,
		private Flusher           $flusher
	)
	{
	}

	public function handle(Command $command)
	{
		$task = $this->taskRepository->get(new Id($command->getId()));
		$project = $this->repository->get(new ProjectId($command->project));

		$task->move($project);

		if ($command->withChildren) {
			$children = $this->taskRepository->allByParent($task->getId());
			foreach ($children as $child) {
				$child->move($project);
			}
		}

		$this->flusher->flush();
	}
}