<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Specify a user style sheet, to load with every page.
 */
final class UserStyleSheets extends ExtraOption
{
    /**
     * @param non-empty-string $path
     */
    public function __construct(string $path)
    {
        parent::__construct(
            repeatable: false,
            command: ['--user-style-sheet', $path],
        );
    }
}
