<?php

namespace App\Model\Work\UseCase\Projects\Task\Executor\Revoke;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\TaskRepository;

class Handler
{
	public function __construct(private TaskRepository   $taskRepository,
								private MemberRepository $memberRepository,
								private Flusher          $flusher)
	{
	}

	public function handle(Command $command)
	{
		$task = $this->taskRepository->get(new Id($command->getId()));
		$member = $this->memberRepository->get(new MemberId($command->getMember()));

		$task->revokeExecutor($member);

		$this->flusher->flush();
	}
}