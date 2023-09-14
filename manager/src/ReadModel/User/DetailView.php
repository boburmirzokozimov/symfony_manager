<?php

namespace App\ReadModel\User;

class DetailView
{
	public $id;
	public $email;
	public $date;
	public $role;
	public $status;
	public $name;

	/**
	 * @var NetworkView[]
	 */
	public ?array $networks = null;

	public function getNetworks(): array
	{
		return $this->networks;
	}
}