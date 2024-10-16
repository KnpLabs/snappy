<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class PageHeightOption implements ExtraOption
{
    public function __construct(public readonly string $height) {}

    public function compile(): array
    {
        return ['--page-height', $this->height];
    }
}
