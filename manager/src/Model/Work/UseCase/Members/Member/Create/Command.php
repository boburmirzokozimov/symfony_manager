<?php

namespace App\Model\Work\UseCase\Members\Member\Create;

use App\Model\User\Entity\User\User;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
	#[NotBlank]
	private $id;

	#[NotBlank]
	private $group;

	#[NotBlank]
	private $firstName;

	#[NotBlank]
	private $lastName;

	#[NotBlank]
	#[Email]
	private $email;

	public function __construct(string $id)
	{
		$this->id = $id;
	}

	public static function fromMember(User $user): self
	{
		$command = new self($user->getId());
		$command->setEmail($user->getEmail());
		$command->setFirstName($user->getName()->getFirst());
		$command->setLastName($user->getName()->getLast());
		return $command;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getGroup()
	{
		return $this->group;
	}

	public function setGroup($group): void
	{
		$this->group = $group;
	}

	public function getFirstName()
	{
		return $this->firstName;
	}

	public function setFirstName($firstName): void
	{
		$this->firstName = $firstName;
	}

	public function getLastName()
	{
		return $this->lastName;
	}

	public function setLastName($lastName): void
	{
		$this->lastName = $lastName;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function setEmail($email): void
	{
		$this->email = $email;
	}
}