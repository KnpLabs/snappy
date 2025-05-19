<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * The header text of the toc.
 *
 * Default: Table of Contents
 */
final class TocHeaderText extends ExtraOption
{
    /**
     * @param non-empty-string $text
     */
    public function __construct(string $text)
    {
        parent::__construct(
            repeatable: false,
            command: ['--toc-header-text', $text]
        );
    }
}
