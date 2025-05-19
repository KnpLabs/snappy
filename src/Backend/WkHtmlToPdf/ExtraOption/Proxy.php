<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Use a proxy.
 */
final class Proxy extends ExtraOption
{
    /**
     * @param non-empty-string $proxy
     */
    public function __construct(string $proxy)
    {
        parent::__construct(
            repeatable: false,
            command: ['--proxy', $proxy]
        );
    }
}
