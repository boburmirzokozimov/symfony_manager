<?php

namespace App\Model\User\UseCase\SignUp\Confirm\Manual;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
	public function __construct(private Flusher $flusher, private UserRepository $userRepository)
	{
	}

	public function handle(Command $command)
	{
		$user = $this->userRepository->get(new Id($command->id));

		$user->confirmSignUp();

		$this->flusher->flush();
	}
}