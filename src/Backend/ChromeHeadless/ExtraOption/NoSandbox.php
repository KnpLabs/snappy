<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

use KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

/**
 * Disable Chrome sandbox (required in Docker/CI environments).
 */
final class NoSandbox extends ExtraOption
{
    public function __construct()
    {
        parent::__construct(
            repeatable: false,
            command: ['--no-sandbox']
        );
    }
}
