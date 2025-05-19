<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Set an additional cookie.
 */
final class Cookie extends ExtraOption
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(string $name, string $value)
    {
        parent::__construct(
            repeatable: true,
            command: ['--cookie', $name, urlencode($value)],
        );
    }
}
