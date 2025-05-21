<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Bypass proxy for host.
 */
final class BypassProxyFor extends ExtraOption
{
    /**
     * @param non-empty-string $value
     */
    public function __construct(string $value)
    {
        parent::__construct(
            repeatable: true,
            command: ['--bypass-proxy-for', $value],
        );
    }
}
