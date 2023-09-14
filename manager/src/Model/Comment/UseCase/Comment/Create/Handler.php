<?php

namespace App\Model\Comment\UseCase\Comment\Create;

use App\Model\Comment\Entity\Comment\AuthorId;
use App\Model\Comment\Entity\Comment\Comment;
use App\Model\Comment\Entity\Comment\CommentRepository;
use App\Model\Comment\Entity\Comment\Entity;
use App\Model\Comment\Entity\Comment\Id;
use App\Model\Flusher;

class Handler
{
    public function __construct(private Flusher $flusher, private CommentRepository $repository)
    {
    }

    public function handle(Command $command): void
    {
        $comment = new Comment(
            Id::next(),
            new  AuthorId($command->author),
            new Entity(
                $command->entityType,
                $command->entityId
            ),
            $command->text
        );

        $this->repository->add($comment);

        $this->flusher->flush();
    }

}