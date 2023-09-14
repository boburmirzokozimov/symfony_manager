<?php

namespace App\Model\Work\UseCase\Projects\Task\Priority;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Task\Id as TaskId;
use App\Model\Work\Entity\Projects\Task\TaskRepository;

class Handler
{
	public function __construct(private TaskRepository $taskRepository,
								private Flusher        $flusher)
	{
	}

	public function handle(Command $command)
	{
		$task = $this->taskRepository->get(new TaskId($command->getId()));

		$task->changePriority($command->priority);

		$this->flusher->flush();
	}
}