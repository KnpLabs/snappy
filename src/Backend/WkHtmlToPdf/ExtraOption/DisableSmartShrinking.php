<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 *  Disable the intelligent shrinking strategy used by WebKit that makes the pixel/dpi ratio non-constant.
 */
final class DisableSmartShrinking extends ExtraOption
{
    public function __construct()
    {
        parent::__construct(
            repeatable: false,
            command: ['--disable-smart-shrinking']
        );
    }
}
