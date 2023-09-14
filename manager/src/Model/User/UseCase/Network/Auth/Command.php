<?php

namespace App\Model\User\UseCase\Network\Auth;

class Command
{
	public string $firstName;
	public string $lastName;
	private string $network;
	private string $identity;

	public function getNetwork(): string
	{
		return $this->network;
	}

	public function setNetwork(string $network): self
	{
		$this->network = $network;

		return $this;
	}

	public function getIdentity(): string
	{
		return $this->identity;
	}

	public function setIdentity(string $identity): self
	{
		$this->identity = $identity;
		return $this;

	}
}
