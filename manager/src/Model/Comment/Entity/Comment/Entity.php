<?php

namespace App\Model\Comment\Entity\Comment;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use Webmozart\Assert\Assert;

#[Embeddable]
class Entity
{
    #[ORM\Column(type: 'string')]
    private string $type;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    public function __construct(string $type, string $id)
    {
        Assert::notNull($type);
        Assert::notNull($id);
        $this->type = $type;
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): string
    {
        return $this->id;
    }
}