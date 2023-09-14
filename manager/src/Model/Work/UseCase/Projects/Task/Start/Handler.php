<?php

namespace App\Model\Work\UseCase\Projects\Task\Start;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use DateTimeImmutable;

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

		$task->start(
			new DateTimeImmutable()
		);

		$this->flusher->flush();
	}
}