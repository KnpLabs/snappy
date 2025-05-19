<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Left aligned footer text.
 */
final class FooterLeft extends ExtraOption
{
    /**
     * @param non-empty-string $text
     */
    public function __construct(string $text)
    {
        parent::__construct(
            repeatable: false,
            command: ['--footer-left', $text]
        );
    }
}
