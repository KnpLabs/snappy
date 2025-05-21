<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Set header font name.
 *
 * Default: Arial
 */
final class HeaderFontName extends ExtraOption
{
    public function __construct(string $fontName)
    {
        parent::__construct(
            repeatable: false,
            command: ['--header-font-name', $fontName]
        );
    }
}
