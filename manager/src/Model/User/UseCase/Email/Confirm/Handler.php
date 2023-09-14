<?php

namespace App\Model\User\UseCase\Email\Confirm;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
	public function __construct(private Flusher $flusher, private UserRepository $repository)
	{
	}

	public function handle(Command $command)
	{
		$user = $this->repository->get(new Id($command->id));

		$user->confirmEmailChanging($command->token);

		$this->flusher->flush();
	}
}