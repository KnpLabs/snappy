<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * HTTP Authentication password.
 */
final class Password extends ExtraOption
{
    /**
     * @param non-empty-string $password
     */
    public function __construct(string $password)
    {
        parent::__construct(
            repeatable: false,
            command: ['--password', $password]
        );
    }
}
