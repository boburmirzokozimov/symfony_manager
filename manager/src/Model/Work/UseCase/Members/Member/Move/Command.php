<?php

namespace App\Model\Work\UseCase\Members\Member\Move;

use App\Model\Work\Entity\Members\Member\Member;

class Command
{

	private string $group;

	public function __construct(private string $id)
	{
	}

	public static function fromMember(Member $member): self
	{
		$command = new self($member->getId());
		$command->setGroup($member->getGroup());
		return $command;
	}

	public function getGroup(): string
	{
		return $this->group;
	}

	public function setGroup(string $group): void
	{
		$this->group = $group;
	}

	public function getId(): string
	{
		return $this->id;
	}
}