<?php

namespace App\Model\Work\UseCase\Members\Member\Archive;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\MemberRepository;

class Handler
{
	public function __construct(private Flusher          $flusher,
								private MemberRepository $repository)
	{
	}

	public function handle(Command $command)
	{
		if (!$member = $this->repository->get(new Id($command->getId()))) {
			throw new \DomainException('Member does not exist');
		}

		$member->archive();

		$this->flusher->flush();
	}
}