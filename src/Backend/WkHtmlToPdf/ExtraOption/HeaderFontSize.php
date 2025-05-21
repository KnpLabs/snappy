<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 *  Set header font size.
 *
 *  Default 12
 */
final class HeaderFontSize extends ExtraOption
{
    /**
     * @param positive-int $size
     */
    public function __construct(int $size)
    {
        parent::__construct(
            repeatable: false,
            command: ['--header-font-size', (string) $size]
        );
    }
}
