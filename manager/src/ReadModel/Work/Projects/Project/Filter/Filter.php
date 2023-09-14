<?php

namespace App\ReadModel\Work\Projects\Project\Filter;

class Filter
{
	public ?string $name = null;
	public ?string $status = null;
	public ?string $member;

	public function __construct(?string $member)
	{
		$this->member = $member;
	}

	public static function all(): self
	{
		return new self(null);
	}

	public static function forMember(string $id): self
	{
		return new self($id);
	}

	public function getMember(): ?string
	{
		return $this->member;
	}
}