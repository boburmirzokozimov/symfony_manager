<?php

namespace App\Model\Work\UseCase\Projects\Task\Plan\Set;

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

		$task->plan($command->plan);

		$this->flusher->flush();
	}
}