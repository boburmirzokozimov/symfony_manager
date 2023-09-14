<?php

namespace App\Model\User\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
class Name
{
    #[ORM\Column(type: 'string')]
    private ?string $first = null;

    #[ORM\Column(type: 'string')]
    private ?string $last = null;

    public function __construct(string $first, string $last)
    {
        Assert::notEmpty($first);
        Assert::notEmpty($last);

        $this->first = $first;
        $this->last = $last;
    }

    public function getFirst(): string
    {
        return $this->first;
    }

    public function getLast(): string
    {
        return $this->last;
    }

    public function __toString(): string
    {
        return $this->last . ' ' . $this->first;
    }
}