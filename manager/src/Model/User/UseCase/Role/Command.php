<?php

namespace App\Model\User\UseCase\Role;

use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
	#[NotBlank]
	public string $id;

	#[NotBlank]
	#[Choice(options: [Role::ADMIN, Role::USER])]
	public string $role;

	public function __construct(string $id)
	{
		$this->id = $id;
	}

	public static function fromUser(User $user): self
	{
		$command = new self($user->getId()->getValue());
		$command->role = $user->getRole();
		return $command;
	}
}
