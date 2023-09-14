<?php

namespace App\Model\Work\Entity\Members\Member;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use JetBrains\PhpStorm\Pure;

class StatusType extends StringType
{
	public const NAME = 'work_members_member_status';

	#[Pure] public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		return $value instanceof Status ? $value->getStatus() : $value;
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		return !empty($value) ? new Status($value) : null;
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