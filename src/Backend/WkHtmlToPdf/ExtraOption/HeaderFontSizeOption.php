<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class HeaderFontSizeOption implements ExtraOption
{
    /**
     * @param positive-int $size
     */
    public function __construct(public readonly int $size) {}

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--header-font-size', $this->size];
    }
}
