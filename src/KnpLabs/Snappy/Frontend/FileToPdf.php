<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Frontend;

use KnpLabs\Snappy\Backend\FileToPdfBackend;
use KnpLabs\Snappy\FileStream;
use SplFileInfo;

class FileToPdf
{
    public function __construct(
        private readonly FileToPdfBackend $backend
    ) {
    }

    public function generateFromFile(SplFileInfo $file, array $options = []): FileStream
    {
        $this->backend->validateOptions($options);

        return $this->backend->generateFromFile($file, $options);
    }
}
