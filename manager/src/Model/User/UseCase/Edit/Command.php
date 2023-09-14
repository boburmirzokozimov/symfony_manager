<?php

namespace App\Model\User\UseCase\Edit;

use App\Model\User\Entity\User\User;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
	#[NotBlank]
	public string $lastName;

	#[NotBlank]
	public string $firstName;

	#[NotBlank]
	#[Email]
	public string $email;

	public function __construct(public string $id)
	{
	}

	public static function fromUser(User $user): self
	{
		$command = new self($user->getId()->getValue());
		$command->email = $user->getEmail() ? $user->getEmail() : null;
		$command->firstName = $user->getName()->getFirst();
		$command->lastName = $user->getName()->getLast();
		return $command;
	}

	public function getId(): string
	{
		return $this->id;
	}
}