<?php

namespace App\Model\User\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "user_user_networks")]
class Network
{
	#[ORM\Id]
	#[ORM\Column(type: "guid")]
	private string $id;

	#[ORM\Column(type: "string", length: 32, unique: true, nullable: true)]
	private string $network;

	#[ORM\Column(type: "string", length: 32, unique: true, nullable: true)]
	private string $identity;

	#[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'networks')]
	#[ORM\JoinColumn(name: "user_id", referencedColumnName: 'id', nullable: false, onDelete: "CASCADE")]
	private User $user;

	public function __construct(User $user, string $network, string $identity)
	{
		$this->id = Uuid::uuid4()->toString();
		$this->user = $user;
		$this->network = $network;
		$this->identity = $identity;
	}

	public function isForNetwork($network): bool
	{
		return $this->network === $network;
	}

	public function getNetwork(): string
	{
		return $this->network;
	}

	public function getIdentity(): string
	{
		return $this->identity;
	}
}