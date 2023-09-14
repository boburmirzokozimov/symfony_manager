<?php

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHasher;
use App\Model\User\Service\SignUpConfirmTokenizer;
use App\Model\User\Service\SignUpConfirmTokenSender;
use DateTimeImmutable;

class Handler
{
	public function __construct(
		private UserRepository           $userRepository,
		private PasswordHasher           $passwordHasher,
		private SignUpConfirmTokenizer   $tokenizer,
		private SignUpConfirmTokenSender $tokenSender,
		private Flusher                  $flusher
	)
	{
	}

	public function handle(Command $command)
	{
		$email = new Email($command->getEmail());

		if ($this->userRepository->existsByEmail($email)) {
			throw new \DomainException('User already exists');
		}

		$user = User::signUpByEmail(
			Id::next(),
			new DateTimeImmutable(),
			$email,
			$this->passwordHasher->hash($command->getPassword()),
			$token = $this->tokenizer->generate(),
			new Name($command->firstName, $command->lastName)
		);

		$this->userRepository->add($user);

		$this->tokenSender->send($email, $token);

		$this->flusher->flush();
	}

}