<?php

namespace App\Model\User\UseCase\Network\Auth;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use DateTimeImmutable;

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
		if ($this->userRepository->hasByNetworkIdentity($command->getNetwork(), $command->getIdentity())) {
			throw new \DomainException('User already exists');
		}

		$user = User::signUpByNetwork(
			Id::next(),
			new DateTimeImmutable(),
			$command->getNetwork(),
			$command->getIdentity(),
			new Name($command->firstName, $command->lastName)
		);

		$this->userRepository->add($user);
		$this->flusher->flush();
	}

}