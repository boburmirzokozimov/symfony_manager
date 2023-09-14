<?php

namespace App\Model\Comment\Entity\Comment;

use Webmozart\Assert\Assert;

class AuthorId
{
    public function __construct(private string $value)
    {
        Assert::notEmpty($this->value);
    }

    public function isEqual(string $other): bool
    {
        return $this->value === $other;
    }

    public function getId(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

}