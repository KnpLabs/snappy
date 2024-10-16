<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\Options\GlobalOptions;

final class CookieJarOption
{
    public function __construct(public readonly string $path) {}

    public function compile(): array
    {
        return ['--no-collate'];
    }
}
