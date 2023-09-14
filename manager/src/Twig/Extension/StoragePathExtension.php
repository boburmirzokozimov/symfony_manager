<?php

namespace App\Twig\Extension;

use App\Service\Uploader\FileUploader;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StoragePathExtension extends AbstractExtension
{
    public function __construct(private FileUploader $uploader)
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('storage_path', [$this, 'path'], ['is_safe' => ['html']])
        ];
    }

    public function path(string $path)
    {
        return $this->uploader->generateUrl($path);
    }
}