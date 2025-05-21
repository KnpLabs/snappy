<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 *  Set the page bottom margin.
 */
final class MarginBottom extends ExtraOption
{
    public function __construct(string $margin)
    {
        parent::__construct(
            repeatable: false,
            command: ['--margin-bottom', $margin]
        );
    }
}
