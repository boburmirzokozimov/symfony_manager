<?php

namespace App\Model\User\UseCase\Reset\Reset;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
	#[NotBlank]
	public string $token;

	#[NotBlank]
	#[Length(min: 6)]
	public string $password;

	public function __construct(string $token)
	{
		$this->token = $token;
	}
}