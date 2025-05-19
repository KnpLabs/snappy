<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Do not link from toc to sections.
 */
final class DisableTocLinks extends ExtraOption
{
    public function __construct()
    {
        parent::__construct(
            repeatable: false,
            command: ['--disable-toc-links']
        );
    }
}
