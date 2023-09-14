<?php

namespace App\Model\Work\UseCase\Projects\Project\Membership\Remove;

class Command
{
	public function __construct(public string $project, public string $member)
	{
	}
}