<?php

namespace App\Model\Work\Entity\Projects\Task;

use Webmozart\Assert\Assert;

class Status
{
	public const NEW = 'new';
	public const WORKING = 'working';
	public const HELP = 'help';
	public const CHECKING = 'checking';
	public const REJECTED = 'rejected';
	public const DONE = 'done';

	public string $value;

	public function __construct(string $name)
	{
		Assert::oneOf($name, [
			self::NEW,
			self::WORKING,
			self::HELP,
			self::CHECKING,
			self::REJECTED,
			self::DONE,
		]);
		$this->value = $name;
	}


	public static function new(): self
	{
		return new self(self::NEW);
	}

	public static function working(): self
	{
		return new self(self::WORKING);
	}

	public function isNew(): bool
	{
		return $this->value == self::NEW;
	}

	public function isDone(): bool
	{
		return $this->value == self::DONE;
	}

	public function isEqual(Status $status): bool
	{
		return $this->value == $status->value;
	}

	public function getValue(): string
	{
		return $this->value;
	}
}