<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

use KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;

/**
 * Set page load timeout in milliseconds.
 */
final class Timeout extends ExtraOption
{
    /**
     * @param int<0, max> $milliseconds
     */
    public function __construct(int $milliseconds)
    {
        parent::__construct(
            repeatable: false,
            command: ['--timeout=' . $milliseconds]
        );
    }
}
