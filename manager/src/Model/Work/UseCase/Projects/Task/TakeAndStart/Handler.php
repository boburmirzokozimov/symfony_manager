<?php

namespace App\Model\Work\UseCase\Projects\Task\TakeAndStart;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Task\Id as TaskId;
use App\Model\Work\Entity\Projects\Task\TaskRepository;

class Handler
{
	public function __construct(private MemberRepository $memberRepository,
								private TaskRepository   $taskRepository,
								private Flusher          $flusher)
	{
	}

	public function handle(Command $command)
	{
		$actor = $this->memberRepository->get(new Id($command->getActor()));
		$task = $this->taskRepository->get(new TaskId($command->getId()));

		if ($task->hasExecutor($actor->getId())) {
			throw new \DomainException('This actor does  exist in this task');
		}

		$task->assignExecutor($actor);

		$task->start(new \DateTimeImmutable());

		$this->flusher->flush();
	}
}