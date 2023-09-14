<?php

namespace App\Model\Work\UseCase\Members\Group\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Group\GroupRepository;
use App\Model\Work\Entity\Members\Group\Id;
use App\Model\Work\Entity\Members\Member\MemberRepository;

class Handler
{
	public function __construct(private Flusher          $flusher,
								private GroupRepository  $repository,
								private MemberRepository $memberRepository)
	{
	}

	public function handle(Command $command)
	{
		if (!$group = $this->repository->get(new Id($command->id))) {
			throw new \DomainException('Unable to find the group');
		};

		if ($this->memberRepository->hasByGroup($group->getId())) {
			throw new \DomainException('Group is not empty');
		}

		$this->repository->remove($group);

		$this->flusher->flush();
	}
}