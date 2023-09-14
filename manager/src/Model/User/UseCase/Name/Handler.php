<?php

namespace App\Model\User\UseCase\Name;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
	public function __construct(private Flusher $flusher, private UserRepository $repository)
	{
	}

	public function handle(Command $command)
	{
		if (!$user = $this->repository->get(new Id($command->id))) {
			throw  new \DomainException('User does not exist');
		};

		$user->changeName(new Name($command->firstName, $command->lastName));

		$this->flusher->flush();
	}
}