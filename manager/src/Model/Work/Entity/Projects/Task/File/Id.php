<?php

namespace App\Model\Work\Entity\Projects\Task\File;

use Ramsey\Uuid\Uuid;

class Id
{
    public function __construct(private string $value)
    {
    }

    public static function next(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqual(string $other): bool
    {
        return $this->value === $other;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}