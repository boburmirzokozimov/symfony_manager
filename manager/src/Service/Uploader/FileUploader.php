<?php

namespace App\Service\Uploader;

use League\Flysystem\FilesystemWriter;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    public function __construct(private FilesystemWriter $storage, private string $baseUrl)
    {
    }

    public function upload(UploadedFile $file): File
    {
        $path = date('Y/m/d');
        $name = Uuid::uuid4()->toString() . '.' . $file->getClientOriginalExtension();

        $this->storage->createDirectory($path);
        $stream = fopen($file->getRealPath(), 'r+');
        $this->storage->writeStream($path . '/' . $name, $stream);
        fclose($stream);

        return new File($path, $name, $file->getSize());
    }

    public function generateUrl(string $path): string
    {
        return $this->baseUrl . '/' . $path;
    }
}