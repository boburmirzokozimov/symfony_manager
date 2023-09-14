<?php

namespace App\Model\User\Service;

use Ramsey\Uuid\Uuid;

class PasswordGenerator
{
	public static function generate(): string
	{
		return Uuid::uuid4()->toString();
	}
}