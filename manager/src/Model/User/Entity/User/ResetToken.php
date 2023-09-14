<?php

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use Webmozart\Assert\Assert;

#[Embeddable]
class ResetToken
{
	#[Column(type: 'string', nullable: true)]
	private string $token;

	#[Column(type: 'date_immutable', nullable: true)]
	private ?DateTimeImmutable $expires = null;

	public function __construct(string $token, DateTimeImmutable $expires)
	{
		Assert::notEmpty($token);
		$this->token = $token;
		$this->expires = $expires;
	}

	public function getToken(): string
	{
		return $this->token;
	}

	public function isExpiredTo(DateTimeImmutable $date): bool
	{
		return $this->expires <= $date;
	}


	public function isEmpty(): bool
	{
		return empty($this->token);
	}

}