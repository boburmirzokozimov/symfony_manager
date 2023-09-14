<?php

namespace App\Model\User\UseCase\Reset\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\ResetTokenizer;
use App\Model\User\Service\ResetTokenSender;
use DateTimeImmutable;

class Handler
{
	public function __construct(
		private UserRepository   $userRepository,
		private ResetTokenizer   $tokenizer,
		private ResetTokenSender $sender,
		private Flusher          $flusher
	)
	{
	}

	public function handle(Command $command)
	{
		$user = $this->userRepository->getByEmail(new Email($command->getEmail()));

		$user->requestPasswordReset(
			$this->tokenizer->generate(),
			new DateTimeImmutable()
		);

		$this->flusher->flush();

		$this->sender->send($user->getEmail(), $user->getResetToken());
	}

}