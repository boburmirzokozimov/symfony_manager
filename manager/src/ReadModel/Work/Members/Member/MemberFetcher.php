<?php

namespace App\ReadModel\Work\Members\Member;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\Status;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\SerializerInterface;

class MemberFetcher
{
    private EntityRepository $repository;

    public function __construct(private Connection             $connection,
                                private SerializerInterface    $serializer,
                                private EntityManagerInterface $em
    )
    {
        $this->repository = $this->em->getRepository(Member::class);
    }

    /**
     * @throws Exception
     */
    public function all()
    {
        $result = $this->connection->createQueryBuilder()
            ->select('m.id',
                'TRIM(CONCAT(m.name_first, \' \' ,m.name_last)) AS name',
                'g.name as group',
                'm.email',
                'm.status',
                '(SELECT COUNT(*) FROM work_projects_project_memberships ms WHERE ms.member_id = m.id) AS membership_count'
            )
            ->from('work_members_members', 'm')
            ->innerJoin('m', 'work_members_groups', 'g', 'm.group_id = g.id')
            ->orderBy('name')
            ->executeQuery()
            ->fetchAllAssociative();

        return $result;
    }

    public function activeGroupedList(): array
    {
        return $this->connection->createQueryBuilder()
            ->from('work_members_members', 'm')
            ->select('m.id',
                'TRIM(CONCAT(m.name_first, \' \' ,m.name_last)) AS name',
                'g.name AS group'
            )
            ->leftJoin('m', 'work_members_groups', 'g', 'g.id = m.group_id')
            ->andWhere('m.status = :status')
            ->setParameter('status', Status::active())
            ->orderBy('g.name')->addOrderBy('name')
            ->executeQuery()
            ->fetchAllAssociative();

    }

    public function activeDepartmentListForProject(string $project_id)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'CONCAT(m.name_first, \'\', m.name_last) AS name',
                'm.id',
                'pd.name AS department'
            )
            ->from('work_members_members', 'm')
            ->innerJoin('m', 'work_projects_project_memberships', 'pm', ' m.id = pm.member_id ')
            ->innerJoin('pm', 'work_projects_project_membership_departments', 'pmd', 'pm.id = pmd.membership_id')
            ->innerJoin('pmd', 'work_projects_project_departments', 'pd', 'pd.id = pmd.department_id')
            ->andWhere('m.status = :status AND pm.project_id = :project')
            ->setParameter('status', Status::ACTIVE)
            ->setParameter('project', $project_id)
            ->orderBy('pd.name')->addOrderBy('name')
            ->executeQuery();

        return $stmt->fetchAllAssociative();
    }

    public function find(string $id): ?Member
    {
        return $this->repository->find($id);
    }
}