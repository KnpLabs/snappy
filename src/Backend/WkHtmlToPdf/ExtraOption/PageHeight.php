<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class PageHeight extends ExtraOption
{
    /**
     * @param non-empty-string $height
     */
    public function __construct(string $height)
    {
        parent::__construct(
            repeatable: false,
            command: ['--page-height', $height],
        );
    }
}
