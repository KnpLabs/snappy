<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Centered footer text.
 */
final class FooterCenter extends ExtraOption
{
    public function __construct(string $text)
    {
        parent::__construct(
            repeatable: false,
            command: ['--footer-center', $text],
        );
    }
}
