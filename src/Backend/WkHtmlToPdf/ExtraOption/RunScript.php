<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Run this additional javascript after the page is done loading.
 */
final class RunScript extends ExtraOption
{
    /**
     * @param non-empty-string $javascript
     */
    public function __construct(string $javascript)
    {
        parent::__construct(
            repeatable: true,
            command: ['--run-script', $javascript]
        );
    }
}
