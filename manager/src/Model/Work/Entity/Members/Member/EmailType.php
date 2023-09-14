<?php

namespace App\Model\Work\Entity\Members\Member;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use JetBrains\PhpStorm\Pure;

class EmailType extends StringType
{
	public const NAME = 'work_members_member_email';

	#[Pure] public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		return $value instanceof Email ? $value->getEmail() : $value;
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		return !empty($value) ? new Email($value) : null;
	}

	public function getName(): string
	{
		return self::NAME;
	}

	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}
}