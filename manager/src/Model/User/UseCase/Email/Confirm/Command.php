<?php

namespace App\Model\User\UseCase\Email\Confirm;

class Command
{
	public function __construct(public string $id, public string $token)
	{
	}
}