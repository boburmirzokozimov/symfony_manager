<?php

namespace App\Model\User\UseCase\Reset\Reset;

use App\Model\Flusher;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHasher;

class Handler
{
	public function __construct(private Flusher        $flusher,
								private UserRepository $repository,
								private PasswordHasher $hasher)
	{
	}

	public function handle(Command $command): void
	{
		if (!$user = $this->repository->findByResetToken($command->token)) {
			throw new \DomainException('Incorrect or confirmed token');
		}

		$user->passwordReset($this->hasher->hash($command->password), new \DateTimeImmutable());

		$this->flusher->flush();
	}
}