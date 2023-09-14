<?php

namespace App\Model\User\UseCase\Create;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordGenerator;
use App\Model\User\Service\PasswordHasher;

class Handler
{
	public function __construct(private UserRepository $repository,
								private Flusher        $flusher,
								private PasswordHasher $passwordHasher)
	{
	}

	public function handle(Command $command)
	{
		$email = new Email($command->email);

		if ($this->repository->existsByEmail($email)) {
			throw new \DomainException('User already exists');
		}

		$user = User::create(
			Id::next(),
			new \DateTimeImmutable(),
			$email,
			$this->passwordHasher->hash(PasswordGenerator::generate()),
			new Name(
				$command->firstName,
				$command->lastName
			)
		);

		$this->repository->add($user);

		$this->flusher->flush();
	}
}