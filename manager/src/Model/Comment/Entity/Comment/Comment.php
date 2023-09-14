<?php

namespace App\Model\Comment\Entity\Comment;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'comment_comments')]
#[ORM\Index(columns: ['date'])]
#[ORM\Index(columns: ['entity_id', 'entity_type'])]
class Comment
{
    #[ORM\Id]
    #[ORM\Column(type: 'comment_comment_id', unique: true)]
    private Id $id;

    #[ORM\Column(type: 'comment_comment_author_id')]
    private AuthorId $authorId;

    #[ORM\Embedded(class: Entity::class)]
    private Entity $entity;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $date;

    #[ORM\Column(type: 'string')]
    private string $text;

    #[ORM\Column(name: 'update_date', type: 'datetime_immutable', nullable: true)]
    private DateTimeImmutable $updateDate;

    public function __construct(Id $id, AuthorId $authorId, Entity $entity, string $text)
    {
        $this->id = $id;
        $this->authorId = $authorId;
        $this->entity = $entity;
        $this->text = $text;
        $this->date = new DateTimeImmutable();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getAuthorId()
    {
        return $this->authorId;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getUpdateDate(): DateTimeImmutable
    {
        return $this->updateDate;
    }

    public function edit(string $text): void
    {
        $this->text = $text;
        $this->updateDate = new DateTimeImmutable();
    }
}