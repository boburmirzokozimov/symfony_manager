<?php

namespace App\Model\Work\Entity\Members\Member;

use Webmozart\Assert\Assert;

class Status
{
	public const ACTIVE = 'active';

	public const ARCHIVED = 'archived';

	private string $status;

	public function __construct(string $status)
	{
		Assert::oneOf($status, [
			self::ACTIVE, self::ARCHIVED
		]);
		$this->status = $status;
	}

	public static function active(): self
	{
		return new self(self::ACTIVE);
	}

	public static function archived(): self
	{
		return new self(self::ARCHIVED);
	}

	public function isActive(): bool
	{
		return $this->status == self::ACTIVE;
	}

	public function isArchived(): bool
	{
		return $this->status == self::ARCHIVED;
	}

	public function isEqual(self $other): bool
	{
		return $this->status == $other->getStatus();
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function __toString(): string
	{
		return $this->status;
	}
}