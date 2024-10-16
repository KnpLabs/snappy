<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class DpiOption implements ExtraOption
{
    /**
     * @param positive-int $dpi
     */
    public function __construct(private readonly int $dpi) {}

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--dpi', $this->dpi];
    }
}
