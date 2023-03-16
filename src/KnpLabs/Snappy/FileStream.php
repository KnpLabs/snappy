<?php

declare(strict_types=1);

namespace KnpLabs\Snappy;

use SplFileInfo;

class FileStream
{
    private SplFileInfo $fileInfo;

    public function __construct(
        string $filePath,
    ) {
        $this->fileInfo = new SplFileInfo($filePath);
    }

    public function __destruct()
    {
        unlink($this->fileInfo->getRealPath());
    }

    public function getFilePath(): string
    {
        return $this->fileInfo->getRealPath();
    }

    public function copyTo(string $destination): void
    {
        copy($this->fileInfo->getRealPath(), $destination);
    }

    public function isEmpty(): bool
    {
        return $this->fileInfo->getSize() === 0;
    }
}
