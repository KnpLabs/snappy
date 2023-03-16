<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Backend;

use KnpLabs\Snappy\Backend;
use KnpLabs\Snappy\FileStream;

interface UriToPdfBackend extends Backend
{
    /**
     * @param iterable<int, string> $options
     */
    public function generateFromUri(string $uri, iterable $options): FileStream;
}
