<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Backend;

use KnpLabs\Snappy\Backend;
use KnpLabs\Snappy\FileStream;
use SplFileInfo;

interface FileToPdfBackend extends Backend
{
    /**
     * @param iterable<int, string> $options
     */
    public function generateFromFile(SplFileInfo $file, iterable $options = []): FileStream;
}
