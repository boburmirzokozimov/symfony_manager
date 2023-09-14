<?php

namespace App\Model\Work\UseCase\Members\Member\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Group\GroupRepository;
use App\Model\Work\Entity\Members\Group\Id as GroupId;
use App\Model\Work\Entity\Members\Member\Email;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Members\Member\Name;

class Handler
{
	public function __construct(private GroupRepository  $groupRepository,
								private MemberRepository $memberRepository,
								private Flusher          $flusher)
	{
	}

	public function handle(Command $command)
	{
		$id = new Id($command->getId());

		if ($this->memberRepository->has($id)) {
			throw new \DomainException('Member already exists');
		}

		$group = $this->groupRepository->get(new GroupId($command->getGroup()));

		$member = new Member(
			$id,
			new Name($command->getFirstName(), $command->getLastName()),
			new Email($command->getEmail()),
			$group
		);

		$this->memberRepository->add($member);

		$this->flusher->flush();
	}
}