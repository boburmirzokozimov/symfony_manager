<?php

namespace App\Model\User\UseCase\SignUp\Confirm\ByToken;

class Command
{
	public string $token;

	public function __construct(string $value)
	{
		$this->token = $value;
	}

	public function getToken(): string
	{
		return $this->token;
	}
}
