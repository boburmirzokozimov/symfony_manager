<?php

namespace App\Model\Comment\UseCase\Comment\Remove;

use App\Model\Comment\Entity\Comment\CommentRepository;
use App\Model\Flusher;

class Handler
{
    public function __construct(private CommentRepository $repository, private Flusher $flusher)
    {
    }

    public function handle(Command $command): void
    {
        $comment = $this->repository->find($command->getComment());

        $this->repository->remove($comment);

        $this->flusher->flush();
    }
}