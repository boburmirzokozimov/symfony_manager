<?php

namespace App\Model\Work\Entity\Members\Member;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use Webmozart\Assert\Assert;

#[Embeddable]
class Name
{
	#[ORM\Column(type: 'string')]
	private string $first;

	#[ORM\Column(type: 'string')]
	private string $last;

	public function __construct(string $first, string $last)
	{
		Assert::notEmpty($first);
		Assert::notEmpty($last);

		$this->first = $first;
		$this->last = $last;
	}

	public function getName(): string
	{
		return $this->first . ' ' . $this->last;
	}

	public function getFirst(): string
	{
		return $this->first;
	}

	public function __toString(): string
	{
		return $this->first . ' ' . $this->last;
	}

	public function getLast(): string
	{
		return $this->last;
	}
}