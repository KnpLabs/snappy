<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Adds a html header.
 */
final class HeaderHtml extends ExtraOption
{
    /**
     * @param non-empty-string $url
     */
    public function __construct(string $url)
    {
        parent::__construct(
            repeatable: false,
            command: ['--header-html', $url],
        );
    }
}
