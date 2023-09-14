<?php

namespace App\Model\Work\Entity\Members\Member;

use JetBrains\PhpStorm\Pure;
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

	public function __toString(): string
	{
		return (string)$this->value;

	}

	#[Pure] public function isEqual(self $id): bool
	{
		return $this->value === $id->getValue();
	}

	public function getValue(): string
	{
		return $this->value;
	}

}