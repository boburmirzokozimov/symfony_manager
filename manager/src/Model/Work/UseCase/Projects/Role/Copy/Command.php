<?php

namespace App\Model\Work\UseCase\Projects\Role\Copy;

use App\Model\Work\Entity\Projects\Role\Role;

class Command
{
	private string $name;

	private array $permission;

	public function __construct(private string $id)
	{
	}

	public static function fromCommand(Role $role)
	{
		$command = new self($role->getId());
		$command->setPermission($role->getPermissions());
		return $command;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getPermission(): array
	{
		return $this->permission;
	}

	public function setPermission(array $permission): void
	{
		$this->permission = $permission;
	}

	public function getId(): string
	{
		return $this->id;
	}

}