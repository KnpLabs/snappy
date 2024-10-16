<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class PageSizeOption implements ExtraOption
{
    public function __construct(public readonly string $size) {}

    public function compile(): array
    {
        return ['--page-size', $this->size];
    }
}
