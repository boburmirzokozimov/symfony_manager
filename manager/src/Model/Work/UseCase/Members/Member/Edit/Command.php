<?php

namespace App\Model\Work\UseCase\Members\Member\Edit;

use App\Model\Work\Entity\Members\Member\Email;
use App\Model\Work\Entity\Members\Member\Member;

class Command
{
	private string $firstName;
	private string $lastName;
	private string $email;

	public function __construct(private string $id)
	{
	}

	public static function fromMember(Member $member): self
	{
		$command = new self($member->getId());
		$command->setFirstName($member->getName()->getFirst());
		$command->setLastName($member->getName()->getLast());
		$command->setEmail($member->getEmail());
		return $command;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function setEmail(Email $email): void
	{
		$this->email = $email;
	}

	public function getFirstName(): string
	{
		return $this->firstName;
	}

	public function setFirstName(string $firstName): void
	{
		$this->firstName = $firstName;
	}

	public function getLastName(): string
	{
		return $this->lastName;
	}

	public function setLastName(string $lastName): void
	{
		$this->lastName = $lastName;
	}

	public function getId(): string
	{
		return $this->id;
	}
}