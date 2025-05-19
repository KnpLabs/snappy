<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Wait until window.status is equal to this string before rendering page.
 */
final class WindowStatus extends ExtraOption
{
    /**
     * @param non-empty-string $windowsStatus
     */
    public function __construct(string $windowsStatus)
    {
        parent::__construct(
            repeatable: false,
            command: ['--window-status', $windowsStatus],
        );
    }
}
