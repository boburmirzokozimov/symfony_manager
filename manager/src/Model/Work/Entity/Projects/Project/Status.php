<?php

namespace App\Model\Work\Entity\Projects\Project;

use Webmozart\Assert\Assert;

class Status
{
	public const ACTIVE = 'active';
	public const ARCHIVED = 'archived';
	public const DELETED = 'deleted';

	public function __construct(private string $name)
	{
		Assert::oneOf($name, [
			self::ACTIVE,
			self::ARCHIVED
		]);
	}

	public static function active(): self
	{
		return new self(self::ACTIVE);
	}

	public static function archive(): self
	{
		return new self(self::ARCHIVED);
	}

	public function isDeleted(): bool
	{
		return $this->name == self::DELETED;
	}

	public function isActive(): bool
	{
		return $this->name == self::ACTIVE;
	}

	public function isArchived(): bool
	{
		return $this->name == self::ARCHIVED;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function __toString(): string
	{
		return (string)$this->name;
	}

}