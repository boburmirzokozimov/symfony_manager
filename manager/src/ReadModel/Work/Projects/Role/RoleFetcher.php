<?php

namespace App\ReadModel\Work\Projects\Role;

use Doctrine\DBAL\Connection;

class RoleFetcher
{
	public function __construct(private Connection $connection)
	{
	}

	public function all()
	{
		$stmt = $this->connection->createQueryBuilder()
			->select(
				'r.id',
				'r.name',
				'r.permissions',
				'(
					SELECT COUNT(*) FROM work_projects_project_membership_roles m WHERE m.role_id = r.id 
				) AS memberships_count'
			)
			->from('work_projects_role', 'r')
			->orderBy('name')
			->executeQuery();

		return array_map(static function (array $role) {
			return array_replace($role, [
				'permissions' => json_decode($role['permissions'], true)
			]);
		}, $stmt->fetchAllAssociative());
	}

	public function allList()
	{
		return $this->connection->createQueryBuilder()
			->select('r.id',
				'r.name')
			->from('work_projects_role', 'r')
			->orderBy('name')
			->executeQuery()
			->fetchAllKeyValue();
	}
}