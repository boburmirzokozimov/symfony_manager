<?php

namespace App\Model\User\UseCase\Reset\Request;

class Command
{
	public string $email;

	public function getEmail(): string
	{
		return $this->email;
	}

	public function setEmail(string $email): void
	{
		$this->email = $email;
	}
}
