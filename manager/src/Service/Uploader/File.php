<?php

namespace App\Service\Uploader;

class File
{
    public function __construct(private string $path, private string $name, private int $size)
    {
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }
}