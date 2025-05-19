<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 *  HTTP Authentication username.
 */
final class Username extends ExtraOption
{
    /**
     * @param non-empty-string $username
     */
    public function __construct(string $username)
    {
        parent::__construct(
            repeatable: false,
            command: ['--username', $username]
        );
    }
}
