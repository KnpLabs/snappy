<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Display line below the header.
 */
final class HeaderLine extends ExtraOption
{
    public function __construct(
    ) {
        parent::__construct(
            repeatable: false,
            command: ['--header-line']
        );
    }
}
