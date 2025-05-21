<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Set footer font size.
 *
 * Default: 12
 */
final class FooterFontSize extends ExtraOption
{
    /**
     * @param positive-int $size
     */
    public function __construct(int $size)
    {
        parent::__construct(
            repeatable: false,
            command: ['--footer-font-size', (string) $size],
        );
    }
}
