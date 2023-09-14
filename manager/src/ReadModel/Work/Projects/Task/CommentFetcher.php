<?php

namespace App\ReadModel\Work\Projects\Task;

use App\Model\Work\Entity\Projects\Task\Task;
use Doctrine\DBAL\Connection;
use Symfony\Component\Serializer\SerializerInterface;

class CommentFetcher
{
    public function __construct(private Connection          $connection,
                                private SerializerInterface $serializer)
    {
    }

    public function allForTask(int $id): ?array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'c.id',
                'm.id as author_id',
                'TRIM(CONCAT(m.name_first, \',\',m.name_last)) as author',
                'c.text',
                'm.email as author_email',
                'c.date'
            )
            ->from('comment_comments', 'c')
            ->innerJoin('c', 'work_members_members', 'm', 'c.author_id = m.id')
            ->where('c.entity_id = :id AND c.entity_type = :type')
            ->setParameter('id', $id)
            ->setParameter('type', Task::class)
            ->orderBy('c.date')
            ->executeQuery()
            ->fetchAllAssociative();

        $view = $this->serializer->denormalize($qb, 'App\ReadModel\Comment\CommentView[]');

        return $view ?: null;
    }
}