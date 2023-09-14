<?php

namespace App\Model\Work\UseCase\Projects\Task\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\TaskRepository;

class Handler
{
	public function __construct(private TaskRepository $repository, private Flusher $flusher)
	{
	}

	public function handle(Command $command)
	{
		if (!$task = $this->repository->get(new Id($command->getId()))) {
			throw new  \DomainException('Task was not found');
		}

		$this->repository->remove($task);

		$this->flusher->flush();
	}
}