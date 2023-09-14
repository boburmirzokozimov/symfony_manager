<?php

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Symfony\Component\Validator\Constraints\Unique;

#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: "user_users")]
#[ORM\Entity()]
class User
{
	public const STATUS_ACTIVE = 'active';
	public const STATUS_BLOCKED = 'blocked';
	public const STATUS_WAIT = 'wait';
	public const STATUS_NEW = 'new';

	#[ORM\Id]
	#[ORM\Column(type: "user_user_id")]
	private Id $id;

	#[ORM\Column(type: 'datetime_immutable')]
	private DateTimeImmutable $date;

	#[Unique]
	#[ORM\Column(type: 'user_user_email', unique: true, nullable: true)]
	private ?Email $email = null;

	#[Unique]
	#[ORM\Column(name: "new_email", type: 'user_user_email', unique: true, nullable: true)]
	private ?Email $newEmail;

	#[Unique]
	#[ORM\Column(name: "new_email_token", type: 'string', nullable: true)]
	private ?string $newEmailToken = null;

	#[ORM\Column(name: "password_hash", type: 'string', nullable: true)]
	private string $passwordHash;

	#[ORM\OneToMany(mappedBy: "user", targetEntity: Network::class, cascade: ['persist'], orphanRemoval: true)]
	private Collection $networks;

	#[ORM\Column(name: "confirm_token", type: 'string', nullable: true)]
	private ?string $confirmToken;

	#[Unique]
	#[ORM\Embedded(class: "ResetToken", columnPrefix: "reset_token_")]
	private ?ResetToken $resetToken = null;

	#[ORM\Embedded(class: "Name")]
	private Name $name;

	#[ORM\Column(type: "string", length: 16)]
	private string $status;

	#[ORM\Column(type: "user_user_role")]
	private Role $role;

	private function __construct(Id $id, DateTimeImmutable $date, Name $name)
	{
		$this->id = $id;
		$this->date = $date;
		$this->name = $name;
		$this->role = Role::user();
		$this->networks = new ArrayCollection();
	}

	public static function create(Id $id, DateTimeImmutable $date, Email $email, string $passwordHash, Name $name): User
	{
		$user = new self($id, $date, $name);
		$user->email = $email;
		$user->passwordHash = $passwordHash;
		$user->status = self::STATUS_ACTIVE;
		return $user;
	}

	public static function signUpByNetwork(Id $id, DateTimeImmutable $date, string $network, string $identity, Name $name): User
	{
		$user = new self($id, $date, $name);
		$user->attachNetwork($network, $identity);
		$user->status = self::STATUS_ACTIVE;
		return $user;
	}

	private function attachNetwork(string $network, string $identity)
	{
		foreach ($this->networks as $existingNetwork) {
			if ($existingNetwork->isForNetwork($network)) {
				throw new DomainException('Network is already attached');
			}
		}
		$this->networks->add(new Network($this, $network, $identity));
	}

	public static function signUpByEmail(Id $id, DateTimeImmutable $date, Email $email, string $passwordHash, string $token, Name $name): User
	{
		$user = new self($id, $date, $name);
		$user->email = $email;
		$user->confirmToken = $token;
		$user->passwordHash = $passwordHash;
		$user->status = self::STATUS_WAIT;
		return $user;
	}


	public function getRole(): string
	{
		return $this->role->getName();
	}

	public function changeRole(Role $role): void
	{
		if ($this->role->isEqual($role)) {
			throw new DomainException('Role is already the same');
		}

		$this->role = $role;
	}

	public function requestPasswordReset(ResetToken $token, DateTimeImmutable $date): void
	{
		if (!$this->isActive()) {
			throw new \DomainException('User is not active.');
		}
		if (!$this->email) {
			throw new \DomainException('Email is not specified.');
		}
		if ($this->resetToken && !$this->resetToken->isExpiredTo($date)) {
			throw new \DomainException('Resetting is already requested.');
		}
		$this->resetToken = $token;
	}

	public function isActive(): bool
	{
		return $this->status === self::STATUS_ACTIVE;
	}

	public function passwordReset(string $password, DateTimeImmutable $date): void
	{
		if (!$this->resetToken) {
			throw new DomainException('Resetting is not requested');
		}
		if (!$this->resetToken->isExpiredTo($date)) {
			throw  new DomainException('Reset token is expired');
		}
		$this->passwordHash = $password;
		$this->resetToken = null;
	}

	public function getDate(): DateTimeImmutable
	{
		return $this->date;
	}

	/**
	 * @return Network[]
	 */
	public function getNetworks(): array
	{
		return $this->networks->toArray();
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function isNew(): bool
	{
		return $this->status === self::STATUS_NEW;
	}

	public function confirmSignup(): void
	{
		if (!$this->isWait()) {
			throw new \DomainException('User is already confirmed');
		}
		$this->status = self::STATUS_ACTIVE;
		$this->confirmToken = null;
	}

	public function isWait(): bool
	{
		return $this->status === self::STATUS_WAIT;
	}

	public function isBlocked(): bool
	{
		return $this->status === self::STATUS_BLOCKED;
	}

	public function getEmail(): ?Email
	{
		return $this->email;
	}

	public function getPasswordHash(): string
	{
		return $this->passwordHash;
	}

	public function getId(): Id
	{
		return $this->id;
	}

	public function getConfirmToken(): string
	{
		return $this->confirmToken;
	}

	public function getResetToken(): ?ResetToken
	{
		return $this->resetToken;
	}

	#[ORM\PostLoad]
	public function checkEmbeds(): void
	{
		if ($this->resetToken->isEmpty()) {
			$this->resetToken = null;
		}
	}

	public function requestEmailChange(Email $email, string $token)
	{
		if (!$this->isActive()) {
			throw new DomainException('User is not active');
		}
		if ($this->email && $this->email->isEqual($email)) {
			throw new DomainException('Email is already in use');
		}
		$this->newEmail = $email;
		$this->newEmailToken = $token;
	}

	public function confirmEmailChanging(string $token)
	{
		if (!$this->newEmailToken) {
			throw new DomainException('Changing is not requested');
		}
		if ($this->newEmailToken !== $token) {
			throw new DomainException('Incorrect changing token');
		}
		$this->email = $this->newEmail;
		$this->newEmailToken = null;
		$this->newEmail = null;
	}

	public function changeName(Name $name)
	{
		$this->name = $name;
	}

	public function getName(): Name
	{
		return $this->name;
	}

	public function editProfile(Name $newName, Email $email)
	{
		$this->name = $newName;
		$this->email = $email;
	}

	public function block()
	{
		if ($this->status === self::STATUS_BLOCKED) {
			throw new DomainException('User is already blocked');
		}

		$this->status = self::STATUS_BLOCKED;
	}

	public function activate()
	{
		if ($this->status === self::STATUS_ACTIVE) {
			throw new DomainException('User is already active');
		}

		$this->status = self::STATUS_ACTIVE;
	}
}
