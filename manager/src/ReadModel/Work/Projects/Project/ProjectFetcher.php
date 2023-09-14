<?php

namespace App\ReadModel\Work\Projects\Project;

use App\ReadModel\Work\Projects\Project\Filter\Filter;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProjectFetcher
{
	public function __construct(private Connection $connection, private PaginatorInterface $paginator)
	{
	}

	public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
	{
		$qb = $this->connection->createQueryBuilder()
			->from('work_projects_projects', 'p')
			->select(
				'p.id',
				'p.name',
				'p.status',
				'p.sort'
			);

		if ($filter->member) {
			$qb->andWhere('EXISTS (
				SELECT ms.member_id FROM work_projects_project_memberships ms WHERE ms.member_id = p.id AND ms.member_id = :member
			)')
				->setParameter('member', $filter->member);
		}

		if ($filter->name) {
			$qb->andWhere($qb->expr()->like('LOWER(name)', ':name'));
			$qb->setParameter('name', '%' . mb_strtolower($filter->name) . '%');
		}

		if ($filter->status) {
			$qb->andWhere('status = :status');
			$qb->setParameter('status', $filter->status);
		}

		if (!\in_array($sort, ['name', 'status'], true)) {
			throw new \UnexpectedValueException('Cannot sort by ' . $sort);
		}

		$qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

		return $this->paginator->paginate($qb, $page, $size);
	}

	public function allList()
	{
		$stmt = $this->connection->createQueryBuilder()
			->select(
				'p.id',
				'p.name',
			)
			->from('work_projects_projects', 'p')
			->executeQuery();

		return $stmt->fetchAllKeyValue();

	}

	public function getMaxSort()
	{
		return (int)$this->connection->createQueryBuilder()
			->select('MAX(p.sort) AS m')
			->from('work_projects_projects', 'p')
			->execute()->fetch()['m'];
	}


}