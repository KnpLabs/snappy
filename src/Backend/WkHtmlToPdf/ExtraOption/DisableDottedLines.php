<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Do not use dotted lines in the toc.
 */
final class DisableDottedLines extends ExtraOption
{
    public function __construct()
    {
        parent::__construct(
            repeatable: false,
            command: ['--disable-dotted-lines'],
        );
    }
}
