<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class MinimumFontSize extends ExtraOption
{
    /**
     * @param int<1, max> $size
     */
    public function __construct(int $size)
    {
        parent::__construct(
            repeatable: false,
            command: ['--minimum-font-size', (string) $size],
        );
    }
}
