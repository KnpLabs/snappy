<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class HeaderFontName implements ExtraOption
{
    public function __construct(public readonly string $fontName) {}

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--header-font-name', $this->fontName];
    }
}
