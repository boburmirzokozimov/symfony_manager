<?php

namespace App\Model\User\UseCase\Create;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
	#[NotBlank]
	public $lastName;

	#[NotBlank]
	public $firstName;

	#[NotBlank]
	#[Email]
	public $email;
}