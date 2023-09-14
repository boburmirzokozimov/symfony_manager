<?php

namespace App\Model\User\UseCase\Active;

class Command
{
	public function __construct(public string $id)
	{
	}
}