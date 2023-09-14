<?php

namespace App\Model\Work\Entity\Projects\Task;

use JetBrains\PhpStorm\Pure;
use Webmozart\Assert\Assert;

class Type
{
	public const NONE = 'none';
	public const FEATURE = 'feature';
	public const ERROR = 'error';

	private string $value;

	public function __construct(string $value)
	{
		Assert::oneOf($value, [
			self::ERROR,
			self::FEATURE,
			self::NONE
		]);
		$this->value = $value;
	}

	public function __toString(): string
	{
		return $this->value;
	}

	#[Pure] public function isEqual(Type $other): bool
	{
		return $this->getValue() === $other->getValue();
	}

	public function getValue(): string
	{
		return $this->value;
	}
}