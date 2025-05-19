<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Set footer font name.
 *
 * Default: Arial
 */
final class FooterFontName extends ExtraOption
{
    /**
     * @param non-empty-string $fontName
     */
    public function __construct(string $fontName)
    {
        parent::__construct(
            repeatable: false,
            command: ['--footer-font-name', $fontName],
        );
    }
}
