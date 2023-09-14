<?php

namespace App\Model\Work\Entity\Projects\Task\File;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Info
{
    #[ORM\Column(type: 'integer')]
    private int $size;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $path;

    public function __construct(string $path, string $name, int $size)
    {
        $this->path = $path;
        $this->name = $name;
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}