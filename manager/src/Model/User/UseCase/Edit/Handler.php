<?php

namespace App\Model\User\UseCase\Edit;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
	public function __construct(private UserRepository $repository,
								private Flusher        $flusher)
	{
	}

	public function handle(Command $command)
	{
		if (!$user = $this->repository->get(new Id($command->getId()))) {
			throw new \DomainException('User does not exist');
		};

		$user->editProfile(
			new Name($command->firstName, $command->lastName),
			new Email($command->email)
		);
		
		$this->flusher->flush();
	}
}