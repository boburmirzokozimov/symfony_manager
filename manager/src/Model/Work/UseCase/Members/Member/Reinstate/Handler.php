<?php

namespace App\Model\Work\UseCase\Members\Member\Reinstate;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\MemberRepository;

class Handler
{
	public function __construct(private MemberRepository $repository,
								private Flusher          $flusher)
	{
	}

	public function handle(Command $command)
	{
		if (!$member = $this->repository->get(new Id($command->getId()))) {
			throw new \DomainException('Member does not exist');
		}

		$member->reinstate();

		$this->flusher->flush();
	}
}