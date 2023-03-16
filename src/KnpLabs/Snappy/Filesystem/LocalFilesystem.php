<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Filesystem;

use KnpLabs\Snappy\Filesystem;
use RuntimeException;

class LocalFilesystem implements Filesystem
{
    public function getFileContents(string $filename): string
    {
        $fileContent = \file_get_contents($filename);

        if (false === $fileContent) {
            throw new RuntimeException(\sprintf('Could not read file \'%s\' content.', $filename));
        }

        return $fileContent;
    }

    public function fileExists(string $filename): bool
    {
        return \file_exists($filename);
    }

    public function isFile(string $filename): bool
    {
        return \strlen($filename) <= \PHP_MAXPATHLEN && \is_file($filename);
    }

    public function filesize(string $filename): int
    {
        $filesize = \filesize($filename);

        if (false === $filesize) {
            throw new RuntimeException(\sprintf('Could not read file \'%s\' size.', $filename));
        }

        return $filesize;
    }

    public function unlink(string $filename): bool
    {
        return $this->fileExists($filename) ? \unlink($filename) : false;
    }

    public function isDir(string $pathname): bool
    {
        return \is_dir($pathname);
    }

    public function mkdir(string $pathname): bool
    {
        return \mkdir($pathname, 0777, true);
    }
}

