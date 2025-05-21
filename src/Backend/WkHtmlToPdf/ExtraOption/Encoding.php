<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Set the default text encoding, for input.
 */
final class Encoding extends ExtraOption
{
    /**
     * @param non-empty-string $encoding
     */
    public function __construct(string $encoding)
    {
        parent::__construct(
            repeatable: false,
            command: ['--encoding', $encoding]
        );
    }
}
