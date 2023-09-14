<?php

namespace App\Model\Work\UseCase\Projects\Task\Create;

use App\Model\Work\Entity\Projects\Task\Type;
use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
	#[NotBlank]
	public $memberId;

	#[NotBlank]
	public $projectId;

	#[NotBlank]
	public $type;

	#[NotBlank]
	public $priority;

	public $name;

	public $content;

	public $parent;

	public $plan;

	public function __construct(string $projectId, string $memberId)
	{
		$this->projectId = $projectId;
		$this->memberId = $memberId;
		$this->type = Type::NONE;
		$this->priority = 2;
	}

}