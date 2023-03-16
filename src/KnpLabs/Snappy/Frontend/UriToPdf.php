<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Frontend;

use KnpLabs\Snappy\Backend\UriToPdfBackend;
use KnpLabs\Snappy\FileStream;

class UriToPdf
{
    public function __construct(
        private readonly UriToPdfBackend $backend
    ) {
    }

    public function generateFromUri(string $file, array $options = []): FileStream
    {
        $this->backend->validateOptions($options);

        return $this->backend->generateFromUri($file, $options);
    }
}

