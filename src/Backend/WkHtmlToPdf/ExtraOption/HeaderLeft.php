<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Left aligned header text.
 */
final class HeaderLeft extends ExtraOption
{
    /**
     * @param non-empty-string $text
     */
    public function __construct(string $text)
    {
        parent::__construct(
            repeatable: false,
            command: ['--header-left', $text]
        );
    }
}
