<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class MarginBottomOption implements ExtraOption
{
    public function __construct(public readonly string $margin) {}

    public function compile(): array
    {
        return ['--margin-bottom', $this->margin];
    }
}
