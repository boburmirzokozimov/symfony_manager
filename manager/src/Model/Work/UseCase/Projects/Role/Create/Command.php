<?php

namespace App\Model\Work\UseCase\Projects\Role\Create;

class Command
{
	public string $name;
	public array $permissions;

	public function getName(): string
	{
		return $this->name;
	}

	public function getPermissions(): array
	{
		return $this->permissions;
	}
}