<?php

namespace App\Model\User\UseCase\SignUp\Request;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
	#[Email]
	#[NotBlank]
	public string $email;

	#[NotBlank]
	public string $firstName;

	#[NotBlank]
	public string $lastName;

	#[NotBlank]
	#[Length(min: 8)]
	public string $password;

	public function getFirstName(): string
	{
		return $this->firstName;
	}

	public function getLastName(): string
	{
		return $this->lastName;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function getPassword(): string
	{
		return $this->password;
	}
}
