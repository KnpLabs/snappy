<?php

declare(strict_types=1);

namespace KnpLabs\Snappy;

interface Filesystem
{
    public function getFileContents(string $filename): string;
    public function fileExists(string $filename): bool;
    public function isFile(string $filename): bool;
    public function filesize(string $filename): int;
    public function unlink(string $filename): bool;
    public function isDir(string $pathname): bool;
    public function mkdir(string $pathname): bool;
}
