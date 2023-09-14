<?php

namespace App\Model\Work\Entity\Members\Member;

use App\Model\Work\Entity\Members\Group\Group;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use JetBrains\PhpStorm\Pure;

#[Table(name: 'work_members_members')]
#[Entity()]
class Member
{
	#[ORM\Id]
	#[ORM\Column(type: 'work_members_member_id')]
	private Id $id;

	#[ORM\ManyToOne(targetEntity: 'App\Model\Work\Entity\Members\Group\Group')]
	#[ORM\JoinColumn(name: "group_id", referencedColumnName: 'id', nullable: false)]
	private Group $group;

	#[ORM\Embedded(class: 'Name')]
	private Name $name;

	#[ORM\Column(type: 'work_members_member_email')]
	private Email $email;

	#[ORM\Column(type: 'work_members_member_status', length: 16)]
	private Status $status;

	public function __construct(Id $id, Name $name, Email $email, Group $group)
	{
		$this->id = $id;
		$this->name = $name;
		$this->email = $email;
		$this->group = $group;
		$this->status = Status::active();
	}

	public function move(Group $group): void
	{
		$this->group = $group;
	}

	public function edit(Name $name, Email $email): void
	{
		$this->name = $name;
		$this->email = $email;
	}

	public function archive(): void
	{
		if ($this->status->isArchived()) {
			throw new \DomainException('User is already archived.');
		}
		$this->status = Status::archived();
	}

	public function reinstate(): void
	{
		if ($this->status->isActive()) {
			throw new \DomainException('User is already active.');
		}
		$this->status = Status::active();
	}

	public function getGroup(): Group
	{
		return $this->group;
	}

	public function getName(): Name
	{
		return $this->name;
	}

	public function getEmail(): Email
	{
		return $this->email;
	}

	public function getStatus(): Status
	{
		return $this->status;
	}

	#[Pure] public function isActive(): bool
	{
		return $this->status->isActive();
	}

	#[Pure] public function isArchived(): bool
	{
		return $this->status->isArchived();
	}

	public function __toString(): string
	{
		return $this->getId()->getValue();
	}

	public function getId(): Id
	{
		return $this->id;
	}
}