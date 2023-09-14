<?php

namespace App\ReadModel\User;

use App\ReadModel\User\Filter\Filter;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use UnexpectedValueException;
use function in_array;

class UserFetcher
{
    public function __construct(private Connection          $connection,
                                private SerializerInterface $serializer,
                                private PaginatorInterface  $paginator)
    {
    }

    public function existByResetToken(string $token): bool
    {
        return $this->connection->createQueryBuilder()
                ->select('COUNT (*)')
                ->from('user_users')
                ->where('reset_token_token = :token')
                ->setParameter('token', $token)
                ->executeStatement() > 0;
    }

    public function findForAuth(string $email): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'email',
                'id',
                'password_hash as password',
                'role',
                'status',
                'TRIM(CONCAT(name_first, \' \' ,name_last)) AS name'
            )
            ->from('user_users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->executeQuery();

        $result = $stmt->fetchAssociative();

        $view = $this->serializer->denormalize($result, AuthView::class);

        return $view ?: null;
    }

    public function findForAuthByNetwork(string $netowork, string $identity): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'u.id',
                'u.email',
                'u.password_hash',
                'u.role',
                'u.status'
            )
            ->from('user_users', 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'n.user_id = u.id')
            ->where('n.network = :network AND n.identity = :identity')
            ->setParameter('network', $netowork)
            ->setParameter('identity', $identity)
            ->executeQuery();

        $result = $stmt->fetchAssociative();

        $view = $this->serializer->denormalize($result, AuthView::class);

        return $view ?: null;
    }

    public function findByEmail(string $email): ?ShortView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'email',
                'id',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->executeQuery();

        $result = $stmt->fetchAssociative();

        $view = $this->serializer->denormalize($result, ShortView::class);

        return $view ?: null;
    }

    public function findDetail(string $id): ?DetailView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'email',
                'id',
                'password_hash',
                'role',
                'status',
                'TRIM(CONCAT(name_first, \' \' ,name_last)) AS name'
            )
            ->from('user_users')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();

        $result = $stmt->fetchAssociative();

        $view = $this->serializer->denormalize($result, DetailView::class);

        $stmt = $this->connection->createQueryBuilder()
            ->select('network', 'identity')
            ->from('user_user_networks')
            ->where('user_id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
        $result = $stmt->fetchAssociative();

        $network = $this->serializer->denormalize($result, NetworkView::class);

        $view->network = $network;

        return $view ?: null;
    }

    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('id',
                'date',
                'TRIM(CONCAT(name_first, \' \', name_last)) AS name',
                'email',
                'role',
                'status'
            )
            ->from('user_users');

        if ($filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(CONCAT(name_first, \' \', name_last))', ':name'));
            $qb->setParameter('name', '%' . mb_strtolower($filter->name) . '%');
        }

        if ($filter->email) {
            $qb->andWhere($qb->expr()->like('LOWER(email)', ':email'));
            $qb->setParameter('email', '%' . mb_strtolower($filter->email) . '%');
        }

        if ($filter->status) {
            $qb->andWhere('status = :status');
            $qb->setParameter('status', $filter->status);
        }

        if ($filter->role) {
            $qb->andWhere('role = :role');
            $qb->setParameter('role', $filter->role);
        }

        if (!in_array($sort, ['date', 'name', 'email', 'role', 'status'], true)) {
            throw new UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}