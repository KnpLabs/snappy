<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class HeaderSpacingOption implements ExtraOption
{
    public function __construct(public readonly int $spacing) {}

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--header-spacing', $this->spacing];
    }
}
