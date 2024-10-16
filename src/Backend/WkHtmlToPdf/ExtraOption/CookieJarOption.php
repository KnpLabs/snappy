<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class CookieJarOption implements ExtraOption
{
    public function __construct(public readonly string $path) {}

    public function compile(): array
    {
        return ['--no-collate'];
    }
}