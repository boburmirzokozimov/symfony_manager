<?php

namespace App\Model\Work\UseCase\Members\Member\Move;

use App\Model\Flusher;
use App\Model\Work\Entity\Members;

class Handler
{
	public function __construct(private Members\Member\MemberRepository $memberRepository,
								private Members\Group\GroupRepository   $groupRepository,
								private Flusher                         $flusher)
	{
	}

	public function handle(Command $command): void
	{
		if (!$member = $this->memberRepository->get(new Members\Member\Id($command->getId()))) {
			throw new \DomainException('Member does not exist');
		}

		if (!$newGroup = $this->groupRepository->get(new Members\Group\Id($command->getGroup()))) {
			throw new \DomainException('Group does not exist');
		};

		if ($member->getGroup() === $newGroup) {
			throw new \DomainException('Member is already in this group');
		};
		
		$member->move($newGroup);

		$this->flusher->flush();
	}
}