<?php

namespace App\Model\Work\UseCase\Projects\Task\ChildOf;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\TaskRepository;

class Handler
{
	public function __construct(private TaskRepository $repository,
								private Flusher        $flusher)
	{
	}

	public function handle(Command $command)
	{
		$task = $this->repository->get(new Id($command->getId()));

		if ($command->parent) {
			$parent = $this->repository->get(new Id($command->parent));
			$task->setChildOf($parent);
		} else {
			$task->setChildOf(null);
		}

		$this->flusher->flush();
	}
}