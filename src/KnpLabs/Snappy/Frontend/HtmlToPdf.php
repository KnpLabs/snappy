<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Frontend;

use KnpLabs\Snappy\Backend\HtmlToPdfBackend;
use KnpLabs\Snappy\FileStream;

class HtmlToPdf
{
    public function __construct(
        private readonly HtmlToPdfBackend $backend,
    ) {
    }

    public function generateFromHtml(string $html, array $options = []): FileStream
    {
        $this->backend->validateOptions($options);

        return $this->backend->generateFromHtml($html, $options);
    }
}
