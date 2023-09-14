<?php

namespace App\Model\User\Entity\User;

use Webmozart\Assert\Assert;

class Email
{
	public function __construct(public string $getEmail)
	{
		Assert::notEmpty($getEmail);
		if (!filter_var($getEmail, FILTER_VALIDATE_EMAIL)) {
			throw new \InvalidArgumentException('Incorrect email');
		}
		$this->getEmail = mb_strtolower($getEmail);
	}

	public function isEqual(Email $email): bool
	{
		return $this->getEmail == $email;
	}

	public function __toString(): string
	{
		return $this->getEmail();
	}

	public function getEmail(): string
	{
		return $this->getEmail;
	}
}