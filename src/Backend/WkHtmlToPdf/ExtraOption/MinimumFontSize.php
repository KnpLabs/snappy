<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

class MinimumFontSize implements ExtraOption
{
    /**
     * @param non-negative-int $size
     */
    public function __construct(private readonly int $size) {}

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--minimum-font-size', $this->size];
    }
}
