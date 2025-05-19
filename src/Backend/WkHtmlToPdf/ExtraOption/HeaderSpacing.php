<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Spacing between header and content in mm.
 *
 * Default: 0
 */
final class HeaderSpacing extends ExtraOption
{
    public function __construct(public readonly int $millimeters)
    {
        parent::__construct(
            repeatable: false,
            command: ['--header-spacing', (string) $millimeters]
        );
    }
}
