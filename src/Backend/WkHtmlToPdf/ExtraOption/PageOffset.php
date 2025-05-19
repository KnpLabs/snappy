<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Set the starting page number.
 *
 * Default: 0
 */
final class PageOffset extends ExtraOption
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(int $offset)
    {
        parent::__construct(
            repeatable: false,
            command: ['--page-offset', (string) $offset]
        );
    }
}
