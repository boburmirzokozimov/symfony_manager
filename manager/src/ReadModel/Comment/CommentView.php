<?php

namespace App\ReadModel\Comment;

use DateTimeImmutable;

class CommentView
{
    public string $id;
    public string $author_id;
    public string $author;
    public string $text;
    public ?string $author_email = '';
    public DateTimeImmutable $date;

    public function getId(): string
    {
        return $this->id;
    }

    public function getAuthorId(): string
    {
        return $this->author_id;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function author_email(): string
    {
        return $this->author_email;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

}