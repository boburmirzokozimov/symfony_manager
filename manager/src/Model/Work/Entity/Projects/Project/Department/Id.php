<?php

namespace App\Model\Work\Entity\Projects\Project\Department;

use Ramsey\Uuid\Uuid;

class Id
{
	public function __construct(private string $value)
	{
	}

	public static function next(): self
	{
		return new self(Uuid::uuid4()->toString());
	}

	public function getValue(): string
	{
		return $this->value;
	}

	public function __toString(): string
	{
		return (string)$this->value;
	}

	public function isEqual(Id $id): bool
	{
		return $this->value == $id;
	}

}