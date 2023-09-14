<?php

namespace App\Model\Work\UseCase\Projects\Role\Edit;

use App\Model\Work\Entity\Projects\Role\Permission;
use App\Model\Work\Entity\Projects\Role\Role;

class Command
{
	/**
	 * @var string[]
	 */
	public $permission;

	public string $name;

	public function __construct(private string $id)
	{
	}

	public static function fromCommand(Role $role): self
	{
		$command = new self($role->getId());
		$command->setName($role->getName());
		$command->permission = array_map(static function (Permission $permission): string {
			return $permission->getName();
		}, $role->getPermissions());
		return $command;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getId(): string
	{
		return $this->id;
	}
}