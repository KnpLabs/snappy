<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * For each level of headings in the toc indent by this length.
 *
 * Default: 1em
 */
final class TocLevelIndentation extends ExtraOption
{
    /**
     * @param non-empty-string $width
     */
    public function __construct(string $width)
    {
        parent::__construct(
            repeatable: false,
            command: ['--toc-level-indentation', $width],
        );
    }
}
