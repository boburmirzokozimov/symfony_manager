<?php

namespace App\Model\User\UseCase\Active;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
	public function __construct(private UserRepository $repository,
								private Flusher        $flusher)
	{
	}

	public function handle(Command $command)
	{
		if (!$user = $this->repository->get(new Id($command->id))) {
			throw new \DomainException('User does not exist');
		}

		$user->activate();

		$this->flusher->flush();
	}
}