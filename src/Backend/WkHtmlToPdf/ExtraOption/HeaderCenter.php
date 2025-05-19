<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 *  Centered header text.
 */
final class HeaderCenter extends ExtraOption
{
    /**
     * @param non-empty-string $text
     */
    public function __construct(string $text)
    {
        parent::__construct(
            repeatable: false,
            command: ['--header-center', $text]
        );
    }
}
