<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Set the page top margin.
 */
final class MarginTop extends ExtraOption
{
    /**
     * @param non-empty-string $margin
     */
    public function __construct(string $margin)
    {
        parent::__construct(
            repeatable: false,
            command: ['--margin-top', $margin],
        );
    }
}
