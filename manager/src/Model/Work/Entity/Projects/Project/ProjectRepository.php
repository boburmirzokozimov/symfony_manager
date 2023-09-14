<?php

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\Work\Entity\Projects\Role\Id as RoleId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class ProjectRepository
{
	private EntityRepository $repository;

	public function __construct(private EntityManagerInterface $em)
	{
		$this->repository = $em->getRepository(Project::class);
	}

	public function get(Id $id): Project
	{
		if (!$project = $this->repository->find($id->getValue())) {
			throw new \DomainException('Project does not exist.');
		};
		return $project;
	}

	public function add(Project $project): void
	{
		$this->em->persist($project);
	}

	public function remove(Project $project): void
	{
		$this->em->remove($project);
	}

	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	public function hasByName(string $getName): bool
	{
		return $this->repository->createQueryBuilder('p')
				->select('COUNT(p.name)')
				->andWhere('p.name = :name')
				->setParameter('name', $getName)
				->getQuery()->getScalarResult() > 0;
	}

	public function hasMembersWithRole(RoleId $getId): bool
	{
		return $this->repository->createQueryBuilder('p')
				->select('p.id')
				->innerJoin('p.memberships', 'ms')
				->innerJoin('ms.roles', 'r')
				->andWhere('r.id = :id')
				->setParameter('id', $getId->getValue())
				->getQuery()->getSingleScalarResult() > 0;

	}
}