<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

use KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

/**
 * Remove default PDF header and footer (date/title and URL/page number).
 */
final class NoPdfHeaderFooter extends ExtraOption
{
    public function __construct()
    {
        parent::__construct(
            repeatable: false,
            command: ['--no-pdf-header-footer']
        );
    }
}
