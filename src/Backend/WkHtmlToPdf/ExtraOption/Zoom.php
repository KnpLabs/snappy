<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Use this zoom factor.
 *
 * Default: 1
 */
final class Zoom extends ExtraOption
{
    public function __construct(float $float)
    {
        parent::__construct(
            repeatable: false,
            command: ['--zoom', (string) $float]
        );
    }
}
