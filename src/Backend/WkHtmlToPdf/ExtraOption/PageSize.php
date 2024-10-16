<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class PageSize implements ExtraOption
{
    public function __construct(public readonly string $size) {}

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--page-size', $this->size];
    }
}
