<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

class BypassProxyFor implements ExtraOption
{
    public function __construct(private readonly string $value) {}

    public function isRepeatable(): bool
    {
        return true;
    }

    public function compile(): array
    {
        return ['--bypass-proxy-for', $this->value];
    }
}
