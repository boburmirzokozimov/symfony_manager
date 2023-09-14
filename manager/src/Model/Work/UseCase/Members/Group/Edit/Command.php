<?php

namespace App\Model\Work\UseCase\Members\Group\Edit;

use App\Model\Work\Entity\Members\Group\Group;

class Command
{
	public string $name;

	public function __construct(public string $id)
	{
	}

	public static function fromGroup(Group $group)
	{
		$command = new self($group->getId()->getValue());
		$command->name = $group->getName();
		return $command;
	}


}