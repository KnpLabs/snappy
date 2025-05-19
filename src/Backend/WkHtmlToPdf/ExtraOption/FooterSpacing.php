<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Spacing between footer and content in mm.
 */
final class FooterSpacing extends ExtraOption
{
    public function __construct(int $millimeters)
    {
        parent::__construct(
            repeatable: false,
            command: ['--footer-spacing', (string) $millimeters]
        );
    }
}
