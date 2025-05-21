<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class PageWidth extends ExtraOption
{
    /**
     * @param non-empty-string $width
     */
    public function __construct(public readonly string $width)
    {
        parent::__construct(
            repeatable: false,
            command: ['--page-width', $width]
        );
    }
}
