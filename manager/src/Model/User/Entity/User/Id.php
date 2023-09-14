<?php

namespace App\Model\User\Entity\User;

use JetBrains\PhpStorm\Pure;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class Id
{
	public function __construct(private string $value)
	{
		Assert::notEmpty($value);
	}

	public static function next(): self
	{
		return new self(Uuid::uuid4()->toString());
	}

	#[Pure] public function __toString(): string
	{
		return $this->getValue();
	}

	public function getValue(): string
	{
		return $this->value;
	}
}