<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class ImageQuality implements ExtraOption
{
    /**
     * @param positive-int $quality
     */
    public function __construct(public readonly int $quality) {}

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--image-quality', $this->quality];
    }
}
