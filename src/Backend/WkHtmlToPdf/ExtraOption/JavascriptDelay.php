<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Wait some milliseconds for javascript finish.
 *
 * Default: 200
 */
final class JavascriptDelay extends ExtraOption
{
    /**
     * @param int<0, max> $milliseconds
     */
    public function __construct(int $milliseconds)
    {
        parent::__construct(
            repeatable: false,
            command: ['--javascript-delay', (string) $milliseconds]
        );
    }
}
