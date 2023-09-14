<?php

namespace App\Model\User\UseCase\Email\Request;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
	#[NotBlank]
	public string $id;

	#[NotBlank]
	#[Email]
	public string $email;

	public function __construct(string $id)
	{
		$this->id = $id;
	}
}