<?php

namespace App\Model\Work\Entity\Members\Group;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "work_members_groups")]
#[ORM\Entity()]
class Group
{
	#[ORM\Id]
	#[ORM\Column(type: 'work_members_group_id')]
	private Id $id;

	#[ORM\Column(type: 'string')]
	private string $name;

	public function __construct(Id $id, string $name)
	{
		$this->id = $id;
		$this->name = $name;
	}

	public function edit(string $name): void
	{
		$this->name = $name;
	}

	public function getId(): Id
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function __toString(): string
	{
		return $this->id;
	}
}