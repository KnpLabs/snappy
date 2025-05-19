<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Web cache directory.
 */
final class CacheDir extends ExtraOption
{
    /**
     * @param non-empty-string $path
     */
    public function __construct(string $path)
    {
        parent::__construct(
            repeatable: false,
            command: ['--cache-dir', $path],
        );
    }
}
