<?php

namespace App\Model\Comment\Entity\Comment;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CommentRepository
{
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository(Comment::class);
    }

    public function add(Comment $comment): void
    {
        $this->em->persist($comment);
    }

    public function remove(Comment $comment): void
    {
        $this->em->remove($comment);
    }

    public function find(string $id): ?Comment
    {
        return $this->repository->find($id);
    }
}