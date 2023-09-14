<?php

namespace App\Model\Work\UseCase\Projects\Task\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Project\Id as ProjectId;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Projects\Task\Id as TaskId;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use App\Model\Work\Entity\Projects\Task\Type;

class Handler
{
	public function __construct(private TaskRepository    $taskRepository,
								private MemberRepository  $memberRepository,
								private ProjectRepository $projectRepository,
								private Flusher           $flusher)
	{
	}

	public function handle(Command $command)
	{
		$member = $this->memberRepository->get(new Id($command->memberId));
		$project = $this->projectRepository->get(new ProjectId($command->projectId));

		$task = new Task(
			$this->taskRepository->nextId(),
			$project,
			$member,
			new \DateTimeImmutable(),
			new Type($command->type),
			$command->priority,
			$command->name,
			$command->content
		);

		if ($command->parent) {
			$parent = $this->taskRepository->get(new TaskId($command->parent));
			$task->setChildOf($parent);
		}

		if ($command->plan) {
			$task->setPlanDate($command->plan);
		}

		$this->taskRepository->add($task);

		$this->flusher->flush();
	}
}