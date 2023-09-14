<?php

namespace App\Model\Work\Entity\Members\Group;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class Id
{
	public function __construct(private string $id)
	{
		Assert::notEmpty($id);
	}

	public static function next(): self
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