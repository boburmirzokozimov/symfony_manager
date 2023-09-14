<?php

namespace App\Model\Work\UseCase\Members\Group\Remove;

class Command
{
	public function __construct(public string $id)
	{
	}
}