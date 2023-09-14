<?php

namespace App\Model\User\UseCase\SignUp\Confirm\ByToken;

use App\Model\Flusher;
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
		if (!$user = $this->userRepository->findByConfirmToken($command->getToken())) {
			throw new \DomainException('Incorrect or confirmed token');
		}

		$user->confirmSignUp();

		$this->flusher->flush();
	}

}