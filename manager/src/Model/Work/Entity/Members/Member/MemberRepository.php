<?php

namespace App\Model\Work\Entity\Members\Member;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use DomainException;

class MemberRepository
{
	private EntityRepository $repository;

	public function __construct(private EntityManagerInterface $em)
	{
		$this->repository = $em->getRepository(Member::class);
	}

	public function get(Id $id): ?Member
	{
		if (!$member = $this->repository->find($id->getValue())) {
			throw new DomainException('Member does not exist');
		}

		return $member;
	}

	public function add(Member $member)
	{
		$this->em->persist($member);
	}

	public function remove(Member $member)
	{
		$this->em->persist($member);
	}

	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	public function has(Id $id): bool
	{
		return $this->repository->createQueryBuilder('t')
				->select('COUNT(t.id)')
				->andWhere('t.id = :id')
				->setParameter(':id', $id->getValue())
				->getQuery()->getSingleScalarResult() > 0;
	}

	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	public function hasByGroup(\App\Model\Work\Entity\Members\Group\Id $id): bool
	{
		return $this->repository->createQueryBuilder('t')
				->select('COUNT(t.id)')
				->andWhere('t.group = :group')
				->setParameter('group', $id)
				->getQuery()->getSingleScalarResult() > 0;
	}
}