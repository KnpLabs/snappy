<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 *  Set paper size to: A4, Letter, etc.
 *
 *  Default: A4
 */
final class PageSize extends ExtraOption
{
    /**
     * @param non-empty-string $size
     */
    public function __construct(string $size)
    {
        parent::__construct(
            repeatable: false,
            command: ['--page-size', $size]
        );
    }
}
