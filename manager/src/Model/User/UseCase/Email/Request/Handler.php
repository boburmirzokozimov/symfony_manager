<?php

namespace App\Model\User\UseCase\Email\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\NewEmailConfirmTokenizer;
use App\Model\User\Service\NewEmailConfirmTokenSender;
use DomainException;

class Handler
{
	public function __construct(private NewEmailConfirmTokenizer   $token,
								private NewEmailConfirmTokenSender $tokenSender,
								private Flusher                    $flusher,
								private UserRepository             $userRepository
	)
	{
	}

	public function handle(Command $command)
	{
		$user = $this->userRepository->get(new Id($command->id));

		$email = new Email($command->email);
		
		if ($this->userRepository->existsByEmail($email)) {
			throw new DomainException('Email is already in use');
		}

		$user->requestEmailChange(
			$email,
			$token = $this->token->generate()
		);

		$this->flusher->flush();

		$this->tokenSender->send($email, $token);
	}
}