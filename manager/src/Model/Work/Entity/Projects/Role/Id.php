<?php

namespace App\Model\Work\Entity\Projects\Role;

use Ramsey\Uuid\Uuid;

class Id
{
	public function __construct(private string $id)
	{
	}

	public static function next()
	{
		return new self(Uuid::uuid4()->toString());
	}

	public function getValue(): string
	{
		return $this->id;
	}

	public function __toString(): string
	{
		return (string)$this->id;
	}

}