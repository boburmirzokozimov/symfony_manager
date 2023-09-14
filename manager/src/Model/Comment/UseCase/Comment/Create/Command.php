<?php

namespace App\Model\Comment\UseCase\Comment\Create;

class Command
{
    public string $author;

    public string $text;

    public string $entityType;

    public string $entityId;

    public function __construct(string $author, string $entityType, string $entityId)
    {
        $this->author = $author;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
    }
}