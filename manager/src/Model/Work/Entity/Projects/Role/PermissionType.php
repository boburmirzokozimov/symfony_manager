<?php

namespace App\Model\Work\Entity\Projects\Role;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use JetBrains\PhpStorm\Pure;

class PermissionType extends JsonType
{
	public const NAME = 'work_projects_role_permissions';

	#[Pure] public static function deserialize(Permission $permission): string
	{
		return $permission->getName();
	}

	public static function serialize(string $permission): ?Permission
	{
		return in_array($permission, Permission::names(), true) ? new Permission($permission) : null;
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if ($value instanceof ArrayCollection) {
			$data = array_map([self::class, 'deserialize'], $value->toArray());
		} else {
			$data = $value;
		}

		return parent::convertToDatabaseValue($data, $platform);
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		if (!is_array($data = parent::convertToPHPValue($value, $platform))) {
			return $data;
		}

		return new ArrayCollection(array_filter(array_map([self::class, 'serialize'], $data)));
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