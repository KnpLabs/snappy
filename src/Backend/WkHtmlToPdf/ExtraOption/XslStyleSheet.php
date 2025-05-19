<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 *  Use the supplied xsl style sheet for printing the table of contents.
 */
final class XslStyleSheet extends ExtraOption
{
    /**
     * @param non-empty-string $file
     */
    public function __construct(string $file)
    {
        parent::__construct(
            repeatable: false,
            command: ['--xsl-style-sheet', $file]
        );
    }
}
