<?php

namespace App\ReadModel\Work\Projects\Task\Filter;

use JetBrains\PhpStorm\Pure;

class Filter
{
	public $member;
	public $author;
	public $name;
	public $project;
	public $type;
	public $status;
	public $priority;
	public $executor;

	public function __construct(?string $project)
	{
		$this->project = $project;
	}

	#[Pure] public static function forProject(string $project): self
	{
		return new self($project);
	}

	public static function all(): self
	{
		return new self(null);
	}

	public function forMember(string $member): self
	{
		$clone = clone $this;
		$clone->member = $member;
		return $clone;
	}
}