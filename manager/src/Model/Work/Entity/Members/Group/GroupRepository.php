<?php

namespace App\Model\Work\Entity\Members\Group;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

class GroupRepository
{
	private EntityRepository $repository;

	public function __construct(private EntityManagerInterface $em)
	{
		$this->repository = $em->getRepository(Group::class);
	}

	/**
	 * @throws EntityNotFoundException
	 */
	public function get(Id $id): Group
	{
		if (!$group = $this->repository->find($id->getValue())) {
			throw new EntityNotFoundException('Group is not found');
		}
		return $group;
	}

	public function add(Group $group): void
	{
		$this->em->persist($group);
	}

	public function remove(Group $group): void
	{
		$this->em->remove($group);
	}

	public function hasByName(string $name): bool
	{
		return $this->repository->createQueryBuilder('g')
				->select('COUNT(g.name)')
				->andWhere('g.name = :name')
				->setParameter('name', $name)
				->getQuery()->getSingleScalarResult() > 0;
	}
}