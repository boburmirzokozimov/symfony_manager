<?php

namespace App\Model\Comment\UseCase\Comment\Edit;

use App\Model\Comment\Entity\Comment\CommentRepository;
use App\Model\Flusher;

class Handler
{
    public function __construct(private Flusher           $flusher,
                                private CommentRepository $repository)
    {
    }

    public function handle(Command $command): void
    {
        $comment = $this->repository->find($command->getComment());

        $comment->edit($command->text);

        $this->flusher->flush();
    }
}