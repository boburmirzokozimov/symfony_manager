<?php

namespace App\ReadModel\Work\Members\Member;

class ShortView
{
	public string $id;
	public string $email;
	public string $name;
	public string $group;
	public string $status;
	public int $membership_count;

	public function getGroup(): string
	{
		return $this->group;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getStatus(): string
	{
		return $this->status;
	}
}