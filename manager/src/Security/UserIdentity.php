<?php

namespace App\Security;

use App\Model\User\Entity\User\User;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
	public function __construct(private ?string $email,
								private ?string $password,
								private ?string $role,
								private ?string $id,
								private ?string $status,
								private ?string $display)
	{
	}

	public function getDisplay(): ?string
	{
		return $this->display;
	}

	public function isActive(): bool
	{
		return $this->status === User::STATUS_ACTIVE;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function getPassword(): ?string
	{
		return $this->password;
	}

	public function getRoles(): array
	{
		return [$this->role];
	}

	public function getUserIdentifier(): string
	{
		return $this->email;
	}

	public function getSalt(): ?string
	{
		return null;
	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials(): void
	{

	}

	public function isEqualTo(UserInterface $user): bool
	{
		if (!$user instanceof self) {
			return false;
		}

		return $this->id === $user->id &&
			$this->status === $user->status &&
			$this->role === $user->role &&
			$this->email === $user->email &&
			$this->password === $user->password;
	}

	public function getId(): string
	{
		return $this->id;
	}
}