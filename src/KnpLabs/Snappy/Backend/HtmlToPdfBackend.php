<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Backend;

use KnpLabs\Snappy\Backend;
use KnpLabs\Snappy\FileStream;

interface HtmlToPdfBackend extends Backend
{
    /**
     * @param iterable<int, string> $options
     */
    public function generateFromHtml(string $html, iterable $options): FileStream;
}
