<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class DpiOptions implements ExtraOption
{
    public function __construct(private readonly int $dpi) {}

    public function compile(): array
    {
        return ['--dpi', $this->dpi];
    }
}
