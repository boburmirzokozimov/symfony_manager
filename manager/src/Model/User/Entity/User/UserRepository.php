<?php

namespace App\Model\User\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class UserRepository
{
	private EntityRepository $repository;

	public function __construct(private EntityManagerInterface $em)
	{
		$this->repository = $em->getRepository(User::class);
	}

	public function add(User $user): void
	{
		$this->em->persist($user);
	}

	public function findByConfirmToken($getToken): ?User
	{
		return $this->repository->findOneBy(['confirmToken' => $getToken]);
	}

	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	public function hasByNetworkIdentity(string $network, string $getIdentity): bool
	{
		return $this->repository->createQueryBuilder('t')
				->select('COUNT(t.id)')
				->innerJoin('t.networks', 'n')
				->andWhere('n.network = :network and n.identity = :identity')
				->setParameter('network', $network)
				->setParameter('identity', $network)
				->getQuery()->getSingleScalarResult() > 0;
	}

	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	public function existsByEmail(Email $email): bool
	{
		return $this->repository->createQueryBuilder('t')
				->select('COUNT(t.id)')
				->andWhere('t.email = :email')
				->setParameter('email', $email->getEmail())
				->getQuery()->getSingleScalarResult() > 0;
	}

	/**
	 * @throws EntityNotFoundException
	 */
	public function getByEmail(Email $email): User
	{
		if (!$user = $this->repository->findOneBy(['email' => $email->getEmail()])) {
			throw new EntityNotFoundException('User is not found');
		}
		return $user;
	}

	/**
	 * @throws EntityNotFoundException
	 */
	public function get(Id $id): User
	{
		if (!$user = $this->repository->find($id->getValue())) {
			throw new EntityNotFoundException('User is not found');
		}
		return $user;
	}

	public function findByResetToken(string $token): ?User
	{
		return $this->repository->findOneBy(['resetToken.token' => $token]);
	}

	public function findByNewEmailResetToken(string $token): ?User
	{
		return $this->repository->findOneBy(['resetToken.token' => $token]);
	}
}