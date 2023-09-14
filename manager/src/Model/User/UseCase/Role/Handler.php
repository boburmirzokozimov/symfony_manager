<?php

namespace App\Model\User\UseCase\Role;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
	public function __construct(
		private UserRepository $userRepository,
		private Flusher        $flusher
	)
	{
	}

	public function handle(Command $command)
	{
		$user = $this->userRepository->get(new Id($command->id));

		/** @var User $user */
		$user->changeRole(new Role($command->role));

		$this->flusher->flush();
	}

}