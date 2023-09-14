<?php

namespace App\Model\Work\Entity\Members\Member;

use Webmozart\Assert\Assert;

class Email
{
	public function __construct(private string $email)
	{
		Assert::notEmpty($email);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new \InvalidArgumentException('Incorrect email');
		}
		$this->email = mb_strtolower($email);
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function __toString(): string
	{
		return $this->email;

	}

	public function isEqual($email): bool
	{
		return $this->email === $email;
	}
}