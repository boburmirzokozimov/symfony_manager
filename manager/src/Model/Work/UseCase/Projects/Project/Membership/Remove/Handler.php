<?php

namespace App\Model\Work\UseCase\Projects\Project\Membership\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;

class Handler
{
	public function __construct(private ProjectRepository $projectRepository,
								private Flusher           $flusher)
	{
	}

	public function handle(Command $command)
	{
		$project = $this->projectRepository->get(new Id($command->project));
		$member = new MemberId($command->member);

		$project->removeMember($member);

		$this->flusher->flush();
	}
}